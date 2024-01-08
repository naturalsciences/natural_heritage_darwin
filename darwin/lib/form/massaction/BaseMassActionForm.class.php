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
		'metadata_ref' => self::getI18N()->__('Change taxonomic metadata'),
        'lithology_ref' => self::getI18N()->__('Change Lithology'),
        'chronostratigraphy_ref' => self::getI18N()->__('Change Chronostratigraphy'),
        'lithostratigraphy_ref' => self::getI18N()->__('Change Lithostratigraphy'),
        'mineralogy_ref' => self::getI18N()->__('Change Mineralogy'),
        'station_visible' => self::getI18N()->__('Change Station visibility'),
        'gtu_ref' => self::getI18N()->__('Change Sampling Location'),
        'ig_ref' => self::getI18N()->__('Change I.G. Num'),
        'expedition_ref' => self::getI18N()->__('Change Expedition'),
        'acquisition_category' => self::getI18N()->__('Change Acquisition Category'),
        'acquisition_date' => self::getI18N()->__('Change Acquisition Date'),
        'gtu_ref' => self::getI18N()->__('Change Sampling Location'),
        'type' => self::getI18N()->__('Change Individual Type'),
        'social_status' => self::getI18N()->__('Change Individual Social Status'),
        'sex' => self::getI18N()->__('Change Individual Sex'),
        'stage' => self::getI18N()->__('Change Individual Stage'),
        'maintenance' => self::getI18N()->__('Add Maintenance'),
	    'informative_workflow' => self::getI18N()->__('Add Suggestion / Problem'),
        'building' => self::getI18N()->__('Change Building'),
        'floor' => self::getI18N()->__('Change Floor'),
        'room' => self::getI18N()->__('Change Room'),
        'row' => self::getI18N()->__('Change Row'),
        'col' => self::getI18N()->__('Change Column'),
        'shelf' => self::getI18N()->__('Change Shelf'),
        'container' => self::getI18N()->__('Change Container'),
        'sub_container' => self::getI18N()->__('Change Sub Container'),
        'ext_links' => self::getI18N()->__('Add an external link'),
        'specimen_status' => self::getI18N()->__('Change Status (lost, damaged,...)'),
        'related_files'  => self::getI18N()->__('Change Related Files visibility/publish state'),
		//JMHerpers 2019 06 04
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

    elseif($action == 'expedition_ref')
      return 'MaExpeditionRefForm';

    elseif($action == 'gtu_ref')
        return 'MaGtuRefForm';

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
    elseif($action == 'ext_links')
      return 'MaExtLinksForm';
    elseif($action == 'specimen_status')
      return 'MaSpStatusForm';
    elseif($action == 'related_files')
      return 'MaRelFilesForm';
	  //JMHerpers 2019 06 04
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
      if($is_admin === false)
	  {
        $query->andWhere('s.id in (select fct_filter_encodable_row(?,?,?))', array(implode(',',$this->getValue('item_list')),'spec_ref', $user_id));
	  }
      else
	  {
        $query->andWhere('s.id in ('. implode(',',$this->getValue('item_list')) .')');
	  }
      $group_action = 0;
      foreach($this->embeddedForms['MassActionForm'] as $key=> $form)
      {
		print($key);
        if (method_exists($this->getEmbeddedForm('MassActionForm')->getEmbeddedForm($key), 'doGroupedAction')) {
          $this->getEmbeddedForm('MassActionForm')->getEmbeddedForm($key)->doGroupedAction($query, $actions_values[$key], $this->getValue('item_list'));
          $group_action++;
        }

        if (method_exists($this->getEmbeddedForm('MassActionForm')->getEmbeddedForm($key), 'doMassAction')) {
          $this->getEmbeddedForm('MassActionForm')->getEmbeddedForm($key)->doMassAction($user_id, $this->getValue('item_list'), $actions_values[$key]);
        }
      }
      if($group_action)
        $query->execute();
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
  

  
  public function add_people($name_action, $num)
  {
	 $tmp=$this->getEmbeddedForm('MassActionForm')->getEmbeddedForm($name_action)->addPeopleValue($num);
	 return $tmp;
	
	
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
	
    if(
      isset($taintedValues['field_action'])
      && is_array(($taintedValues['field_action']))
      && count($taintedValues['field_action']) != 0
      && in_array('related_files', $taintedValues['field_action'])
      && !isset($taintedValues['MassActionForm']['related_files'])
    ) {
      $taintedValues['MassActionForm']['related_files']['visible'] = "";
      $taintedValues['MassActionForm']['related_files']['publishable'] = "";
    }
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
