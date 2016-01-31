<?php
class TagLineForm extends BaseForm
{
  public function configure()
  {
    $this->setWidget('tag',new sfWidgetFormInputText(array(),  array('class' => 'tag_line_'.$this->options['num'])));
    $this->setValidator('tag', new sfValidatorString(array('required' => false, 'trim' => true)) );
  }
}
