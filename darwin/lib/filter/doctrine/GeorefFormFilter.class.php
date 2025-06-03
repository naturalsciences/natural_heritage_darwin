<?php

/**
 * Georef filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
class GeorefFormFilter extends BaseGeorefFormFilter
{
  /**
   * @see DarwinModelFormFilter
   */
  public function configure()
  {
	  
	  
    parent::configure();
	$this->widgetSchema['tags'] = new sfWidgetFormInputText();
	 $this->validatorSchema['tags'] = new sfValidatorString(array('required' => false, 'trim' => FALSE));
	 
	 $this->widgetSchema['tag_boolean'] = new sfWidgetFormChoice(array('choices' => array('OR' => 'OR', 'AND' => 'AND')));
	$this->widgetSchema['tag_boolean']->setDefault(array("or"));
	$this->validatorSchema['tag_boolean'] = new sfValidatorPass();
	
	 $subForm = new sfForm();
    $this->embedForm('Tags',$subForm);
  }
  
    public function addValue($num)
  {
      $form = new TagLineForm(null,array('num'=>$num));
      $this->embeddedForms['Tags']->embedForm($num, $form);
      $this->embedForm('Tags', $this->embeddedForms['Tags']);
  }
}
