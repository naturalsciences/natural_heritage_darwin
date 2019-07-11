<?php

/**
 * OldMultimedia filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseOldMultimediaFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['is_digital'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_digital'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sub_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sub_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['title'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['title'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['title_indexed'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['title_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['subject'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['subject'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['coverage'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['coverage'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['apercu_path'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['apercu_path'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['copyright'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['copyright'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['license'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['license'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['uri'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['uri'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['descriptive_ts'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['descriptive_ts'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['descriptive_language_full_text'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['descriptive_language_full_text'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['creation_date'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['creation_date'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false))));

    $this->widgetSchema   ['creation_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['creation_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['publication_date_from'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['publication_date_from'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false))));

    $this->widgetSchema   ['publication_date_from_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['publication_date_from_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['publication_date_to'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['publication_date_to'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false))));

    $this->widgetSchema   ['publication_date_to_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['publication_date_to_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['parent_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['path'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['path'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['mime_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['mime_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('old_multimedia_filters[%s]');
  }

  public function getModelName()
  {
    return 'OldMultimedia';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'is_digital' => 'Boolean',
      'type' => 'Text',
      'sub_type' => 'Text',
      'title' => 'Text',
      'title_indexed' => 'Text',
      'subject' => 'Text',
      'coverage' => 'Text',
      'apercu_path' => 'Text',
      'copyright' => 'Text',
      'license' => 'Text',
      'uri' => 'Text',
      'descriptive_ts' => 'Text',
      'descriptive_language_full_text' => 'Text',
      'creation_date' => 'Date',
      'creation_date_mask' => 'Number',
      'publication_date_from' => 'Date',
      'publication_date_from_mask' => 'Number',
      'publication_date_to' => 'Date',
      'publication_date_to_mask' => 'Number',
      'parent_ref' => 'Number',
      'path' => 'Text',
      'mime_type' => 'Text',
    ));
  }
}
