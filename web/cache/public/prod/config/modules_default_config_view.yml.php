<?php
// auto-generated by sfViewConfigHandler
// date: 2018/02/13 10:31:17
$response = $this->context->getResponse();


  $templateName = sfConfig::get('symfony.view.'.$this->moduleName.'_'.$this->actionName.'_template', $this->actionName);
  $this->setTemplate($templateName.$this->viewName.$this->getExtension());



  if (null !== $layout = sfConfig::get('symfony.view.'.$this->moduleName.'_'.$this->actionName.'_layout'))
  {
    $this->setDecoratorTemplate(false === $layout ? false : $layout.$this->getExtension());
  }
  else if (null === $this->getDecoratorTemplate() && !$this->context->getRequest()->isXmlHttpRequest())
  {
    $this->setDecoratorTemplate('' == 'layout' ? false : 'layout'.$this->getExtension());
  }
  $response->addHttpMeta('content-type', 'text/html', false);
  $response->addMeta('title', 'Darwin 2', false, false);

  $response->addStylesheet('reset.css', '', array ());
  $response->addStylesheet('menu_pub.css', '', array ());
  $response->addStylesheet('content.css', '', array ());
  $response->addJavascript('jquery-1.7.1.min.js', '', array ());
  $response->addJavascript('jquery.ui.all.min.js', '', array ());
  $response->addJavascript('core.js', '', array ());


