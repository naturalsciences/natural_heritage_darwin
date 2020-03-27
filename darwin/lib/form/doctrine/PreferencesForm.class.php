<?php

/**
 * Preferences form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PreferencesForm extends BaseForm
{
  public function configure()
  {
    $pref_keys = array('search_cols_specimen', 'board_search_rec_pp',
      'board_spec_rec_pp','help_message_activated','default_search_rec_pp', 'exclude_prefix_in_searches');
    $this->db_keys = Doctrine_Core::getTable('Preferences')->getAllPreferences($this->options['user']->getId(), $pref_keys);
    $is_reg_user = $this->options['user']->isA(Users::REGISTERED_USER) ;
    $choices = Doctrine_Core::getTable('MySavedSearches')->getAllFields('specimen') ;
    $choices = $this->translateValues($choices);

    $this->widgetSchema['search_cols_specimen'] = new sfWidgetFormChoice(array(
      'choices' => $choices,
      'expanded' => true,
      'multiple' => true,
      'renderer_options' => array('formatter' => array($this, 'formatter'))
    ));
    $this->widgetSchema['search_cols_specimen']->setLabel('Specimens default visible columns');
    $default = $this->db_keys['search_cols_specimen'];
    if($default == '')
      $default = Doctrine_Core::getTable('Preferences')->getDefaultValue('search_cols_specimen');
    $this->widgetSchema['search_cols_specimen']->setDefault(explode('|',$default));
    $this->widgetSchema->setHelp('search_cols_specimen', 'Define which field will be available by default into the specimen search');
    $this->validatorSchema['search_cols_specimen'] = new sfValidatorChoice(array('choices' => array_keys($choices), 'multiple' => true));

    $choices = array('5' => '5', '10' => '10', '15' => '15', '20' => '20','50'=>'50', '100'=>'100');
    $this->widgetSchema['board_search_rec_pp'] = new sfWidgetFormChoice(array('choices' => $choices));
    $this->validatorSchema['board_search_rec_pp'] = new sfValidatorChoice(array('choices' => array_keys($choices) ));
    $this->widgetSchema['board_search_rec_pp']->setLabel('Number of saved searches');
    $this->widgetSchema->setHelp('board_search_rec_pp',"Number of Saved searches showed on the board widget. (You browse every searches on the dedicated page)");
    $this->widgetSchema['board_search_rec_pp']->setDefault($this->db_keys['board_search_rec_pp']? $this->db_keys['board_search_rec_pp'] : Doctrine_Core::getTable('Preferences')->getDefaultValue('board_search_rec_pp'));

    $this->widgetSchema['board_spec_rec_pp'] = new sfWidgetFormChoice(array('choices' => $choices));
    $this->validatorSchema['board_spec_rec_pp'] = new sfValidatorChoice(array('choices' => array_keys($choices) ));
    $this->widgetSchema['board_spec_rec_pp']->setLabel('Number of saved specimens');
    $this->widgetSchema->setHelp('board_spec_rec_pp',"Number of saved specimens list showed on the board widget. (You browse every specimen lists on the dedicated page)");
    $this->widgetSchema['board_spec_rec_pp']->setDefault($this->db_keys['board_spec_rec_pp']? $this->db_keys['board_spec_rec_pp'] : Doctrine_Core::getTable('Preferences')->getDefaultValue('board_spec_rec_pp'));


    $this->widgetSchema['default_search_rec_pp'] = new sfWidgetFormChoice(array('choices' => $choices));
    $this->validatorSchema['default_search_rec_pp'] = new sfValidatorChoice(array('choices' => array_keys($choices) ));
    $this->widgetSchema['default_search_rec_pp']->setLabel('Number of records per pages');
    $this->widgetSchema['default_search_rec_pp']->setDefault($this->db_keys['default_search_rec_pp']? $this->db_keys['default_search_rec_pp'] : Doctrine_Core::getTable('Preferences')->getDefaultValue('default_search_rec_pp'));


    $this->widgetSchema['help_message_activated'] = new sfWidgetFormInputCheckbox() ;
    $this->widgetSchema->setHelp('help_message_activated',"Display help icons in forms or hide icons");
    $this->widgetSchema['help_message_activated']->setDefault((boolean)$this->db_keys['help_message_activated']) ;
    $this->widgetSchema['help_message_activated']->setLabel("Display help icons") ;
    $this->validatorSchema['help_message_activated'] = new sfValidatorboolean() ;
    
    $this->widgetSchema['exclude_prefix_in_searches'] = new sfWidgetFormInputCheckbox() ;
    $this->widgetSchema->setHelp('exclude_prefix_in_searches',"exclude collection prefixes in specimens searches");
    $this->widgetSchema['exclude_prefix_in_searches']->setDefault((boolean)$this->db_keys['exclude_prefix_in_searches']) ;
    $this->widgetSchema['exclude_prefix_in_searches']->setLabel("Exclude prefix in search") ;
    $this->validatorSchema['exclude_prefix_in_searches'] = new sfValidatorboolean() ;

    $this->widgetSchema->setNameFormat('preferences[%s]');
  }

  public function formatter($widget, $inputs)
  {
    $rows = array();
    foreach ($inputs as $i => $input)
    {
      $rows[] = $widget->renderContentTag(
            'tr',
            '<td>'.$input['label'].'</td><td>'.$input['input'].'</td>'
           );
    }
    return $widget->renderContentTag('tbody', implode($widget->getOption('separator'), $rows));
  }

  public function save($con = null)
  {
    $results = array(
      'search_cols_specimen'=> implode('|',$this->getValue('search_cols_specimen')),
      'board_search_rec_pp'=> $this->getValue('board_search_rec_pp'),
      'board_spec_rec_pp'=> $this->getValue('board_spec_rec_pp'),
      'default_search_rec_pp'=> $this->getValue('default_search_rec_pp'),
      'help_message_activated' => intval($this->getValue('help_message_activated')),
      'exclude_prefix_in_searches' => intval($this->getValue('exclude_prefix_in_searches')),
    );
    Doctrine_Core::getTable('Preferences')->saveAllPreferences($this->options['user']->getId(),$results);
  }
}
