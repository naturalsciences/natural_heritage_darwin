<?php

/**
 * TaxonomicIdentifications form base class.
 *
 * @method TaxonomicIdentifications getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTaxonomicIdentificationsForm extends IdentificationsForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('taxonomic_identifications[%s]');
  }

  public function getModelName()
  {
    return 'TaxonomicIdentifications';
  }

}
