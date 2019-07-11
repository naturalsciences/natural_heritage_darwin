<?php

/**
 * Properties filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PropertiesFormFilter extends BasePropertiesFormFilter
{
  public function configure()
  {
    $this->useFields(array('referenced_relation', 'property_type', 'applies_to', 'lower_value', 'upper_value', 'property_unit'));

    $this->addPagerItems();

    $this->widgetSchema['property_type'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'Properties',
      'table_method' => array('method'=>'getDistinctType', 'parameters'=> array() ),
      'add_empty' => $this->getI18N()->__('All')
    ));
    $this->validatorSchema['property_type'] = new sfValidatorString(array('required' => false));


    $this->widgetSchema['applies_to'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'Properties',
      'table_method' => array('method'=>'getDistinctApplies', 'parameters'=> array() ),
      'add_empty' => $this->getI18N()->__('All')
    ));

    $this->validatorSchema['applies_to'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['lower_value'] = new sfWidgetFormInput();
    $this->validatorSchema['lower_value'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['upper_value'] = new sfWidgetFormInput();
    $this->validatorSchema['upper_value'] = new sfValidatorString(array('required' => false));


    $this->widgetSchema['property_unit'] = new widgetFormSelectComplete(array(
      'model' => 'Properties',
      'table_method' => array('method' => 'getDistinctUnit', 'parameters' => array(/*$this->options['ref_relation']*/)),
      'add_empty' => true,
      'change_label' => 'Pick a unit in the list',
      'add_label' => 'Add another unit',
    ));

    //Find smth for loans, loan_items, collections, spec?
    $this->allowed_relations = array('','specimens', 'expeditions','taxonomy', 'lithology','lithostratigraphy',
      'chronostratigraphy', 'mineralogy', 'people', 'insurances', 'igs', 'gtu', 'bibliography',
    );
    $this->allowed_relations = array_combine($this->allowed_relations, $this->allowed_relations );

    $this->widgetSchema['referenced_relation'] = new sfWidgetFormChoice(array('choices'=> $this->allowed_relations));
    $this->validatorSchema['referenced_relation'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($this->allowed_relations)));

    $this->validatorSchema['property_unit'] = new sfValidatorString(array('required' => false));
    $this->widgetSchema->setLabels(array(
      'referenced_relation' => 'Linked Info',
    ));
  }

  public function addValuesColumnQuery($query, $field, $val)
  {
    $sql_part = array();
    $sql_params = array();
    $value_from = $val['lower_value'];
    $value_to = $val['upper_value'];
    $unit = $val['property_unit'];
    // We have only 1 Value
    if($value_from != '' && $value_to == '') {
      if($unit == '') {
        $sql_part[] = '  ( lower_value = ? OR  upper_value = ?) ';
        $sql_params[] = $value_from;
        $sql_params[] = $value_from;
      //We don't know the filed unit
      } elseif(Properties::searchRecognizedUnitsGroups($unit) === false) {
        $sql_part[] = '  ( lower_value = ? OR  upper_value = ?) AND property_unit = ? ';
        $sql_params[] = $value_from;
        $sql_params[] = $value_from;
        $sql_params[] = $unit;

      } else { // Recognized unit
        $sql_params[] = $value_from;
        $sql_params[] = $unit;
        $sql_params[] = $unit;

        $unitGroupStr =  implode(',',array_fill(0,count($unitGroup),'?'));
        $sql_part[] = ' ( convert_to_unified ( ?,  ? ) BETWEEN lower_value_unified AND  upper_value_unified) AND is_property_unit_in_group(property_unit, ?)  ';
      }
    }
    // We have 2 Values
    elseif($value_from != '' && $value_to != '') {
      if($unit == '') {
        $sql_part[] = ' ( ( lower_value = ? OR  upper_value = ?) OR ( lower_value = ? OR  upper_value = ?) )';
        $sql_params[] = $value_from;
        $sql_params[] = $value_from;
        $sql_params[] = $value_to;
        $sql_params[] = $value_to;
      //We don't know the filed unit
      } elseif(Properties::searchRecognizedUnitsGroups($unit) === false) {
        $sql_part[] = ' ( ( lower_value = ? OR  upper_value = ?) OR ( lower_value = ? OR  upper_value = ?) )  AND property_unit = ? ';
        $sql_params[] = $value_from;
        $sql_params[] = $value_from;
        $sql_params[] = $value_to;
        $sql_params[] = $value_to;
        $sql_params[] = $unit;

      } else { // Recognized unit
        $conn_MGR = Doctrine_Manager::connection();
        $lv = $conn_MGR->quote($value_from, 'string');
        $uv = $conn_MGR->quote($value_to, 'string');
        $unit = $conn_MGR->quote($unit, 'string');
        $sql_part[] = "
            (
              ( lower_value_unified BETWEEN convert_to_unified($lv,$unit) AND convert_to_unified($uv,$unit))
              OR
              ( upper_value_unified BETWEEN convert_to_unified($lv,$unit) AND convert_to_unified($uv,$unit))
            )
            OR
            (
              lower_value_unified BETWEEN 0 AND convert_to_unified($lv,$unit)
              AND
              upper_value_unified BETWEEN convert_to_unified($uv,$unit) AND 'Infinity'
        )";
        $query->andWhere("is_property_unit_in_group(property_unit,$unit)") ;
      }
    }
    if(!empty($sql_part))
      $query->andWhere(implode(' AND ', $sql_part), $sql_params ) ;
    return $query ;
  }

  public function addPropertyTypeColumnQuery($query, $field, $val)
  {
    if($val != '') {
      $query->andWhere("property_type = ?", $val);
    }
    return $query ;
  }

  public function addCustomReferencedRelationColumnQuery($query, $field, $val)
  {
    if($val != '') {
      $query->andWhere("referenced_relation = ?", $val);
    } else {
      $query->andWhereIn('referenced_relation', array_keys($this->allowed_relations));
    }
    return $query ;
  }

  public function addAppliesToColumnQuery($query, $field, $val)
  {
    if($val != '') {
      $query->andWhere("applies_to = ?", $val);
    }
    return $query ;
  }

  public function addPropertyUnitColumnQuery($query, $field, $val)
  {
    if($val != '') {
      $query->andWhere("property_unit = ?", $val);
    }
    return $query ;
  }

  public function doBuildQuery(array $values)
  {
    $query = parent::doBuildQuery($values);
    $this->addCustomReferencedRelationColumnQuery($query, 'referenced_relation', $values['referenced_relation']);
    $this->addValuesColumnQuery($query, 'values', $values);
    return $query ;
  }
}
