<?php

/**
 * StagingRelationship form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class StagingRelationshipForm extends BaseStagingRelationshipForm
{
  public function configure()
  {
    $name = $this->getObject()->getInstitutionName() ;
    $this->widgetSchema['institution_ref'] = new widgetFormButtonRef(array(
       'model' => 'Institutions',
       'link_url' => 'institution/choose?with_js=1',
       'method' => 'getFamilyName',
       'box_title' => $this->getI18N()->__('Choose Institution'),
       'nullable' => false,
       'default_name' => $name,
       'button_class'=>'',
     ),
      array('class'=>'inline',
           )
    );
    $this->validatorSchema['institution_ref'] = new sfValidatorInteger(array('required'=>false));
    $this->widgetSchema['relationship_type'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();
    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['record_id'] = new sfValidatorInteger();
    $this->widgetSchema['unit_type'] = new sfWidgetFormInputHidden(array('default'=>'specimens'));
    $this->validatorSchema['unit_type'] = new sfValidatorString(array('required'=>false));
    $this->validatorSchema['id'] = new sfValidatorInteger(array('required'=>false));
  }
}
