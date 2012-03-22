<?php

/**
 * Identifiers form.
 *
 * @package    form
 * @subpackage Identifiers
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class BiblioAssociationsForm extends BaseCatalogueBibliographyForm
{

  public function configure()
  {
    $bib_id= $this->getObject()->getBibliographyRef() ;
    $this->widgetSchema['bibliography_ref'] = new sfWidgetFormInputHidden();
    if($bib_id) {
      $this->widgetSchema['bibliography_ref']->setLabel(Doctrine::getTable('Bibliography')->find($bib_id)->getTitle()) ;
    }
    else {
      $this->widgetSchema['bibliography_ref']->setAttribute('class','hidden_record');
    }

    $this->validatorSchema['bibliography_ref'] = new sfValidatorInteger(array('required'=>false));
    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();
    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();    
    $this->validatorSchema['record_id'] = new sfValidatorInteger();
    $this->validatorSchema['id'] = new sfValidatorInteger(array('required'=>false));

    $this->mergePostValidator(new BiblioValidatorSchema());

  }

}
