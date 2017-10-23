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
    $this->useFields(array('bibliography_ref'));
    $bib_id= $this->getObject()->getBibliographyRef() ;
    $this->widgetSchema['bibliography_ref'] = new sfWidgetFormInputHidden();
    if($bib_id) {
      $this->widgetSchema['bibliography_ref']->setLabel(Doctrine::getTable('Bibliography')->find($bib_id)->getTitle()) ;
    }
    else {
      $this->widgetSchema['bibliography_ref']->setAttribute('class','hidden_record');
    }

    $this->validatorSchema['bibliography_ref'] = new sfValidatorInteger(array('required'=>false));
    $this->validatorSchema['id'] = new sfValidatorInteger(array('required'=>false));

    $this->mergePostValidator(new BiblioValidatorSchema());

  }

}
