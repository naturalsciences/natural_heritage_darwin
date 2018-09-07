<?php

/**
 * PeopleLanguages form.
 *
 * @package    form
 * @subpackage PeopleLanguages
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class PeopleLanguagesForm extends BasePeopleLanguagesForm
{
  public function configure()
  {
    unset($this['id']);
    $this->widgetSchema['people_ref'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['language_country'] = new sfWidgetFormI18nChoiceLanguage(array('culture' => $this->getCurrentCulture()));
    $this->validatorSchema['language_country'] = new sfValidatorI18nChoiceLanguage(array('required' => true) );
  }
}
