<?php
// auto-generated by sfDefineEnvironmentConfigHandler
// date: 2017/11/17 17:27:59
sfConfig::add(array(
  'dw_salt' => 'salt',
  'dw_yearRangeMin' => 1700,
  'dw_yearRangeMax' => 2019,
  'dw_dateUpperBound' => '2038/12/31',
  'dw_dateLowerBound' => '0001/01/01',
  'dw_yearUpperBound' => '2038',
  'dw_recPerPage' => 10,
  'dw_pagerSlidingSize' => 15,
  'dw_catalogueRecLimit' => 500,
  'dw_queryTimeout' => 30000,
  'dw_defaultInstitutionRef' => 1,
  'dw_mailer_class' => 'sfMailer',
  'dw_mailer_param' => array (
  'logging' => '%SF_LOGGING_ENABLED%',
  'charset' => 'utf-8',
  'delivery_strategy' => 'none',
  'transport' => 
  array (
    'class' => 'Swift_SmtpTransport',
    'param' => 
    array (
      'host' => 'smtp.africamuseum.be',
      'port' => 25,
      'encryption' => NULL,
      'username' => NULL,
      'password' => NULL,
    ),
  ),
),
  'dw_recaptcha_public_key' => '6Lf_ZL0SAAAAAN5AS3Wiiiu3VOs6CmpW3VMgZauH',
  'dw_recaptcha_private_key' => '6Lf_ZL0SAAAAAEP1fcqjynm6NH3wbj1hc1It8iCH',
  'dw_recaptcha_proxy_host' => 'YourProxyHostIPIfOne',
  'dw_recaptcha_proxy_port' => 3128,
  'dw_tracking_enabled' => true,
  'dw_analytics_enabled' => false,
  'dw_analytics_code' => 'UA-XXXXX-X',
  'dw_preview_max_size' => 10,
));
