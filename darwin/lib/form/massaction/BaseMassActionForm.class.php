<?php

class BaseMassActionForm extends sfFormSymfony
{
  protected static function getI18N()
  {
    return sfContext::getInstance()->getI18N();
  }

  public function getActionTitle($action)
  {
    $poss_actions = self::getPossibleActions(true);
    $poss_actions[$action];
  }

  public static function getPossibleActions()
  {
    $result = array(
        'collection_ref' => self::getI18N()->__('Change Collection'),
		'restricted_access' => self::getI18N()->__('Change public access'),
        'taxon_ref' => self::getI18N()->__('Change Taxonomy'),
        'lithology_ref' => self::getI18N()->__('Change Lithology'),
        'chronostratigraphy_ref' => self::getI18N()->__('Change Chronostratigraphy'),
        'lithostratigraphy_ref' => self::getI18N()->__('Change Lithostratigraphy'),
        'mineralogy_ref' => self::getI18N()->__('Change Mineralogy'),
        'station_visible' => self::getI18N()->__('Change Station visibility'),
        'ig_ref' => self::getI18N()->__('Change I.G. Num'),
        'acquisition_category' => self::getI18N()->__('Change Acquisition Category'),
        'acquisition_date' => self::getI18N()->__('Change Acquisition Date'),

        'type' => self::getI18N()->__('Change Individual Type'),
        'social_status' => self::getI18N()->__('Change Individual Social Status'),
        'sex' => self::getI18N()->__('Change Individual Sex'),
        'stage' => self::getI18N()->__('Change Individual Stage'),

        'maintenance' => self::getI18N()->__('Add Maintenance'),
		'informative_workflow' => self::getI18N()->__('Add Suggestion / Problem'),
		'specimen_part' => self::getI18N()->__('Change Specimen part'),
        'building' => self::getI18N()->__('Change Building'),
        'floor' => self::getI18N()->__('Change Floor'),
        'room' => self::getI18N()->__('Change Room'),
        'row' => self::getI18N()->__('Change Row'),
        'col' => self::getI18N()->__('Change Column'),
        'shelf' => self::getI18N()->__('Change Shelf'),
        'container' => self::getI18N()->__('Change Container'),
        'sub_container' => self::getI18N()->__('Change Sub Container'),
		'nagoya_specimen' => self::getI18N()->__('Change Nagoya in specimens'),
        'add_property' => self::getI18N()->__('Add property in specimen'),
        'add_gtu_tag' => self::getI18N()->__('Add Locality tag'),
        'sampling_date' => self::getI18N()->__('Change Sampling date'),
		'collectors' => self::getI18N()->__('Replace collectors'),
		'donators' => self::getI18N()->__('Replace donators of sellers'),
    );
    return $result;
  }

  protected function getFormNameForAction($action)
  {
    if($action == 'collection_ref')
      return 'MaCollectionRefForm';
    if($action == 'restricted_access')
      return 'MaRestrictedAccessForm';
	  
    elseif($action == 'taxon_ref')
      return 'MaTaxonomyRefForm';

    elseif($action == 'lithology_ref')
      return 'MaLithologyRefForm';

    elseif($action == 'ig_ref')
      return 'MaIgRefForm';

    elseif($action == 'chronostratigraphy_ref')
      return 'MaChronostratigraphyRefForm';

    elseif($action == 'lithostratigraphy_ref')
      return 'MaLithostratigraphyRefForm';

    elseif($action == 'mineralogy_ref')
      return 'MaMineralogyRefForm';

    elseif($action == 'station_visible')
      return 'MaStationVisibleForm';
    elseif($action == 'acquisition_category')
      return 'MaAcquisitionCategoryForm';
    elseif($action == 'acquisition_date')
      return 'MaAcquisitionDateForm';

    elseif($action == 'type')
      return 'MaTypeForm';
    elseif($action == 'social_status')
      return 'MaSocialStatusForm';
    elseif($action == 'sex')
      return 'MaSexForm';
    elseif($action == 'stage')
      return 'MaStageForm';

    elseif($action == 'maintenance')
      return 'MaMaintenanceForm';
    elseif($action == 'informative_workflow')
      return 'MaInformativeWorkflowForm';
	elseif($action == 'specimen_part')
      return 'MaSpecimenPartForm';
    elseif($action == 'building')
      return 'MaBuildingForm';
    elseif($action == 'floor')
      return 'MaFloorForm';
    elseif($action == 'room')
      return 'MaRoomForm';
    elseif($action == 'row')
      return 'MaRowForm';
    elseif($action == 'shelf')
      return 'MaShelfForm';
    elseif($action == 'col')
      return 'MaColForm';
    elseif($action == 'container')
      return 'MaContainerForm';
    elseif($action == 'sub_container')
      return 'MaSubContainerForm';
	elseif($action == 'nagoya_specimen')
      return 'MaNagoyaSpecForm';
   elseif($action == 'add_property')
      return 'MaAddPropertyForm';
   elseif($action == 'add_gtu_tag')
      return 'MaAddGtuTagForm';
   elseif($action == 'sampling_date')
      return 'MaSamplingDateForm';
   elseif($action == 'collectors')
      return 'MaCollectorForm';	
   elseif($action == 'donators')
      return 'MaDonatorForm';
    else
      return 'sfForm';
  }

  public function doMassAction($user_id, $is_admin = false)
  {
  
  
    if($this->isBound() && $this->isValid())
    {
       $_SESSION['mass_action_messages']=Array();
      $actions_values = $this->getValue('MassActionForm');

      $query = Doctrine_Query::create()->update('Specimens s');
      //ftheeten 2017 07 27
	  //$query_test_storage=Doctrine_Query::create()->select('StorageParts p')->andWhere();
	  
      $query_storage=Doctrine_Query::create()->update('StorageParts p');
      if($is_admin == false)
      {
        $query->andWhere('s.id in (select fct_filter_encodable_row(?,?,?))', array(implode(',',$this->getValue('item_list')),'spec_ref', $user_id));
        //ftheeten 2017 07 27
        $query_storage->andWhere('p.specimen_ref in (select fct_filter_encodable_row(?,?,?))', array(implode(',',$this->getValue('item_list')),'spec_ref', $user_id));
      }
      else
      {
        $query->andWhere('s.id in ('. implode(',',$this->getValue('item_list')) .')');
        $query_storage->andWhere('p.specimen_ref in ('. implode(',',$this->getValue('item_list')) .')');
      }
      $group_action_specimen = 0;
      $group_action_storage = 0;
      foreach($this->embeddedForms['MassActionForm'] as $key=> $form)
      {
        if (method_exists($this->getEmbeddedForm('MassActionForm')->getEmbeddedForm($key), 'doGroupedAction')) 
        {
           //ftheeten 2017 07 27
           $tableTmp=Array();
           $tableTmp['s']='Specimens';
           if (method_exists($this->getEmbeddedForm('MassActionForm')->getEmbeddedForm($key), 'getTable'))
           {
                $tableTmp=$this->getEmbeddedForm('MassActionForm')->getEmbeddedForm($key)->getTable();
           }
          if(array_key_exists("s", $tableTmp))
          {
              $this->getEmbeddedForm('MassActionForm')->getEmbeddedForm($key)->doGroupedAction($query, $actions_values[$key], $this->getValue('item_list'));
              $group_action_specimen++;
           }
           //ftheeten 2017 07 27 storage
           elseif(array_key_exists("p", $tableTmp))
           {
				//2023 12 14 check only one part by specimen
				$key_specimens=$this->getValue('item_list');
				$go=true;
				$list_errors=[];
				foreach($key_specimens as $pk)
				{
					$parts= Doctrine_Core::getTable('StorageParts')->findBySpecimenRef( $pk);
					$nb_parts=count($parts);
					print($nb_parts);
					if($nb_parts>1)
					{
						$go=false;
						//$_SESSION['mass_action_messages'][]="Error : one specimen has more than one storage part, can't update";
						$list_errors[]="http://darwin/backend.php/specimen/view/id/".$pk;
						
					}
				}
				if($go)
				{
		   
					$this->getEmbeddedForm('MassActionForm')->getEmbeddedForm($key)->doGroupedAction($query_storage, $actions_values[$key], $this->getValue('item_list'));
					$group_action_storage++;
				}
				else
				{
					$_SESSION['mass_action_messages'][]="Error : one specimen at least has more than one storage part, can't update.  Check ".implode(" ", $list_errors);
				}
              
           }
        }

        if (method_exists($this->getEmbeddedForm('MassActionForm')->getEmbeddedForm($key), 'doMassAction')) 
        {
          $this->getEmbeddedForm('MassActionForm')->getEmbeddedForm($key)->doMassAction($user_id, $this->getValue('item_list'), $actions_values[$key]);
        }
      }
		  //ftheeten 2017 07 27
		  if($group_action_specimen)
		  {
			$query->execute();
		  }
		 if($group_action_storage>0)
		 {
			$query_storage->execute();
		 }
    
	}
  }

  public function addSubForm($field_name)
  {

    $form_name = $this->getFormNameForAction($field_name);
    $subForm = new $form_name();

    $this->embeddedForms['MassActionForm']->embedForm($field_name, $subForm);
      //Re-embedding the container
    $this->embedForm('MassActionForm', $this->embeddedForms['MassActionForm']);
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    if(isset($taintedValues['field_action']) && is_array(($taintedValues['field_action'])) && count($taintedValues['field_action']) != 0
      && isset($taintedValues['MassActionForm']) && is_array(($taintedValues['MassActionForm'])) && count($taintedValues['MassActionForm']) != 0 )
    {
      foreach($taintedValues['field_action'] as $form_name)
      {
          $this->addSubForm($form_name);
      }
    }
    parent::bind($taintedValues,$taintedFiles);
  }


  public function add_people($name_action, $num)
  {
	 $tmp=$this->getEmbeddedForm('MassActionForm')->getEmbeddedForm($name_action)->addPeopleValue($num);
	 return $tmp;
	
	
  }
  public function configure()
  {
    sfWidgetFormSchema::setDefaultFormFormatterName('list');
    $this->widgetSchema->setNameFormat('mass_action[%s]');

    $this->widgetSchema['field_action'] = new sfWidgetFormSelectCheckbox(array(
     'choices' =>  self::getPossibleActions(),
     'template' => '<div class="group_%group% fld_group"><label>%group%</label> %options%</div>',
    ));

    $this->validatorSchema['field_action'] = new sfValidatorPass();

    $this->widgetSchema['item_list'] =  new sfWidgetFormChoice(array( 'choices' => array() ));


    $this->validatorSchema['item_list'] = new sfValidatorDoctrineChoice(array(
      'multiple' => true,
      'model' => 'Specimens',
      'min' => 0,
    ));

    $subForm = new sfForm();
    $this->embedForm('MassActionForm',$subForm);
  }
}
