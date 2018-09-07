<?php 
class savedValidator extends sfValidatorSchema
{
    public function __construct($options = array(), $messages = array())
    {
        parent::__construct(null, $options, $messages);
    }
    
    protected function doClean($values)
    {
        throw new sfValidatorError($this, 'invalid');
    }
}
