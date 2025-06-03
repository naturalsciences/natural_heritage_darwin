<?php

/**
 * GtuToCountry form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
class GtuToCountryForm extends BaseGtuToCountryForm
{
  /**
   * @see DarwinModelForm
   */
  public function configure()
  {
	  $this->widgetSchema['country_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'GtuCountry',
        'table_method' => "getSortedIso3166",
		'key_method' => 'getId',
		'method' => 'getIso3166Label',
        'add_empty' => true
      ),
      array('class'=>'catalogue_level select_iso_country')
      );
	   
	   $this->widgetSchema['gtu_ref'] = new sfWidgetFormInputHidden();
	  $this->validatorSchema['country_ref']= new sfValidatorPass();

	  $this->validatorSchema['gtu_ref']= new sfValidatorPass();
    parent::configure();
  }
  
   public function bind(array $taintedValues = null, array $taintedFiles = null)
    {

			$go=true;
			if(!array_key_exists("country_ref",$taintedValues))
		    {
				$go=false;
			}
			elseif(trim($taintedValues["country_ref"])=="")
			{
				$go=false;
			}
			elseif(!is_numeric($taintedValues["country_ref"]))
			{
				$go=false;
			}
			if($go)
			{
			 parent::bind($taintedValues, $taintedFiles);
			}
	}
}
