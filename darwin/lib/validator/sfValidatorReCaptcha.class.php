<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorReCaptcha validates a ReCaptcha.
 *
 * This validator uses ReCaptcha: http://recaptcha.net/
 *
 * The ReCaptcha API documentation can be found at http://recaptcha.net/apidocs/captcha/
 *
 * To be able to use this validator, you need an API key: http://recaptcha.net/api/getkey
 *
 * To create a captcha validator:
 *
 *    $captcha = new sfValidatorReCaptcha(array('private_key' => RECAPTCHA_PRIVATE_KEY));
 *
 * where RECAPTCHA_PRIVATE_KEY is the ReCaptcha private key.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class sfValidatorReCaptcha extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * private_key:    The ReCaptcha private key (required)
   *  * remote_addr:    The remote address of the user
   *  * server_host:    The ReCaptcha server host
   *  * server_port:    The ReCaptcha server port
   *  * server_path:    The ReCatpcha server path
   *  * server_timeout: The timeout to use when contacting the ReCaptcha server
   *
   * Available error codes:
   *
   *  * captcha
   *  * server_problem
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
  
    $this->addOption('private_key');

    $this->addOption('remote_addr');
    $this->addOption('server_host', 'www.google.com/recaptcha/api');
    $this->addOption('server_port', 80);
    $this->addOption('server_path', '/siteverify');
    $this->addOption('server_timeout', 10);
    $this->addOption('proxy_host', '');
    $this->addOption('proxy_port', 80);
    $this->addMessage('captcha', 'The captcha is not valid (%error%).');
    $this->addMessage('server_problem', 'Unable to check the captcha from the server (%error%).');
    
    //$this->setOption('required', false);
  }

  /**
   * Cleans the input value.
   *
   * The input value must be an array with 2 required keys: recaptcha_challenge_field and recaptcha_response_field.
   *
   * It always returns null.
   *
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {

        $captcha=$_REQUEST['g-recaptcha-response'];
        $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$this->getOption('private_key')."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
        $g_response = json_decode($response);
        if($g_response->success===true) {
            return true;
         }
         else
         {
            throw new sfValidatorError($this, 'server_problem', array('error' => "Check captcha"));
         }
         
         return false;
   
  }

 
}
