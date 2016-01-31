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
      if($this->user) {
        return $values;
      }
      elseif(sfConfig::get('app_ldap_ldap_enabled', false) === true) {
        $ldap = new ldapAuth();
        $values['username'] = strtolower($values['username']);
        if( $ldap->authenticate($values['username'], $values['password']) === true ) {
          $this->user = Doctrine::getTable('Users')->getUserByLogin($values['username'], 'ldap');
          //We don't know the user yet but be is known on the LDAP
          if( !$this->user) {

            $name_attr = sfConfig::get('app_ldap_attr_displayname', 'displayName');
            $mail_attr = sfConfig::get('app_ldap_attr_mail', 'mail');

            $infos = $ldap->getAttributes($values['username'], array($name_attr, $mail_attr ));
            $this->user = new Users();
            $this->user->setDbUserType( Users::REGISTERED_USER);
             $this->user->setFamilyName('');
            if($name_attr)
              $this->user->setGivenName(isset($infos[$name_attr]) ? $infos[$name_attr]: '-');

            $this->user->UsersLoginInfos[0]->setUserName($values['username']);
            $this->user->UsersLoginInfos[0]->setLoginType('ldap');
            if($mail_attr && isset($infos[$mail_attr])) {
              $this->user->UsersComm[0]->setCommType('e-mail');
              $this->user->UsersComm[0]->setEntry($infos[$mail_attr]);
            }
            $this->user->save();
            $this->user->addUserWidgets();
          }
        }
      }

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
