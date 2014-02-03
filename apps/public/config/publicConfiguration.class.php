<?php

class publicConfiguration extends sfApplicationConfiguration
{
  protected $backendRouting = null;

  public function configure() {
    require_once($this->getConfigCache()->checkConfig('config/darwin.yml'));
    require_once($this->getConfigCache()->checkConfig('config/import_versions.yml'));

  }

  public function generateBackendUrl($name, $parameters = array(), $request=null)
  {
    $env_str = '';
    switch($this->getEnvironment())
    {
      case 'prod': $env_str = '';break;
      case 'dev': $env_str = '_dev';break;
      case 'preprod': $env_str = '_pre';break;
    }
    if($request)
      $server = $request->getHost();
    else
      $server = $_SERVER['SERVER_NAME'];

    return 'http://'.$server.'/backend'.$env_str.'.php'.$this->getBackendRouting()->generate($name, $parameters);
  }

  public function getBackendRouting()
  {
    if (!$this->backendRouting)
    {
      $this->backendRouting = new sfPatternRouting(new sfEventDispatcher());

      $config = new sfRoutingConfigHandler();
      $routes = $config->evaluate(array(sfConfig::get('sf_apps_dir').'/backend/config/routing.yml'));
      $this->backendRouting->setRoutes($routes);
    }

    return $this->backendRouting;
  }
}
