<?php

/**
 * GeoreferencesByService form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
class GeoreferencesByServiceForm extends BaseGeoreferencesByServiceForm
{
  /**
   * @see DarwinModelForm
   */
  public function configure()
  {
	  
	$this->useFields(array('data_origin', 'wfs_url','wfs_table', 'wfs_id', 'name','query_date', 'tag_group_name', 'tag_sub_group_name', 'geom_wkt', 'validation_level', 'validation_comment'  ));
	
	 $sources = array('WFS_SERVICE'=>'WFS Service','DARWIN_TEMPLATE'=>'Darwin template','USER'=> 'Drawn by user', 'GAZETTEER' => 'Gazetteer', 'OTHER'=> 'other');
	 $this->widgetSchema['data_origin'] = new sfWidgetFormChoice(array('choices'=> $sources));
	 $this->widgetSchema['wfs_url'] = new sfWidgetFormInput();
	 $this->widgetSchema['wfs_table'] = new sfWidgetFormInput();
	 $this->widgetSchema['wfs_id'] = new sfWidgetFormInput();
	 $this->widgetSchema['name'] = new sfWidgetFormInput();
	 $this->widgetSchema['name']->setLabel("Name of feature");
	 
	 $groups=Array();
	 foreach(TagGroups::getGroups() as $k => $v)
	{
		$groups[$k]=$v;
	}
	$this->widgetSchema['tag_group_name'] = new sfWidgetFormChoice(array('choices'=> $groups));


	 $this->widgetSchema['query_date'] = new sfWidgetFormInput();
	  $this->widgetSchema['query_date']->setLabel("Date query");
	 $this->widgetSchema['query_date']->setAttributes(array( "readonly"=>"readonly"));
	 
	 $this->widgetSchema['tag_sub_group_name'] = new widgetFormSelectComplete(array(
      'model' => 'TagGroups',
      'change_label' => 'Pick a sub-type in the list',
      'add_label' => 'Add another sub-type',
    ));

    $this->widgetSchema['tag_sub_group_name']->setOption('forced_choices', Doctrine_Core::getTable('TagGroups')->getDistinctSubGroups($this->getObject()->getTagGroupName()) );
	
	$validation = array('PENDING'=>'Pending','ACCEPTED'=>'Accepted','ACCEPTED_AFTER_CORRECTION'=>'Accepted after correction','TO_CHECK'=> 'To_check', 'REFUSED' => 'refused', 'OTHER'=> 'other');
	$this->widgetSchema['validation_level'] = new sfWidgetFormChoice(array('choices'=> $validation));
    parent::configure();
  }
}
