<?php

/**
 * GtuToCountry form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
class GtuToCountrySubForm extends GtuToCountryForm
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
      array('class'=>'catalogue_level')
      );
	   $this->widgetSchema['gtu_ref'] = new sfWidgetFormInputHidden();

	  $this->validatorSchema['country_ref']= new sfValidatorPass();
	  $this->validatorSchema['gtu_ref']= new sfValidatorPass();
    parent::configure();
  }
}
