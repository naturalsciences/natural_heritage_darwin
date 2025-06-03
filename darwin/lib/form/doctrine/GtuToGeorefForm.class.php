<?php

/**
 * GtuToGeoref form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
class GtuToGeorefForm extends BaseGtuToGeorefForm
{
  /**
   * @see DarwinModelForm
   */
  public function configure()
  {
  
  $this->widgetSchema['id'] = new sfWidgetFormInputText();
   
     $this->widgetSchema['gtu_ref'] = new sfWidgetFormInputText();
	 $this->widgetSchema['gtu_ref']->setAttributes(array('class' => 'linked_gtu'));
	 $this->widgetSchema['georef_ref'] = new sfWidgetFormInputText();
	  $this->widgetSchema['description'] = new sfWidgetFormInputText();
	 
	 $this->validatorSchema['id']= new sfValidatorPass();
	 $this->validatorSchema['gtu_ref']= new sfValidatorPass();
	 $this->validatorSchema['georef']= new sfValidatorPass();
	 $this->validatorSchema['description']= new sfValidatorPass();
    parent::configure();
  }
  
   public function bind(array $taintedValues = null, array $taintedFiles = null)
    {

			$go=true;
			if(!array_key_exists("gtu_ref",$taintedValues))
		    {
				$go=false;
			}
			elseif(trim($taintedValues["gtu_ref"])=="")
			{
				$go=false;
			}
			elseif(!is_numeric($taintedValues["gtu_ref"]))
			{
				$go=false;
			}
			if($go)
			{
			 parent::bind($taintedValues, $taintedFiles);
			}
	}
}
