<?php
class LoginForm extends BaseForm
{
  public function configure()
  {

    $this->setWidgets(array(
      'username'    => new sfWidgetFormInputText(),
      'password'   => new sfWidgetFormInputPassword(),
    ));

    $this->setValidators(array(
      'username'    => new sfValidatorString(
        array(
          'required' => true,
          'min_length' => 4, 'trim' => true),
          array('min_length' => '"%value%" must be at least %min_length% characters.')
        ),
        'password'   => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('login[%s]');
    $this->validatorSchema->setPostValidator(
      new sfValidatorCallback(array('callback' => array($this, 'checkPassword')))
    );

  }

  public function checkPassword($validator, $values)
  {
    if(! empty($values['username']) )
    {
        $this->user = Doctrine::getTable('Users')->getUserByPassword($values['username'], $values['password']);
        if (! $this->user)
        {
            $error = new sfValidatorError($validator, 'Bad login or password');
            // throw an error bound to the password field
            throw new sfValidatorErrorSchema($validator, array('global' => $error));
        }
    }
    return $values;
  }

}
