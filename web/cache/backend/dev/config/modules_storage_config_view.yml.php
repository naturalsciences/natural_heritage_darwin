<?php
// auto-generated by sfViewConfigHandler
// date: 2018/03/09 08:48:33
$response = $this->context->getResponse();

if ($this->actionName.$this->viewName == 'editSuccess')
{
  $templateName = sfConfig::get('symfony.view.'.$this->moduleName.'_'.$this->actionName.'_template', $this->actionName);
  $this->setTemplate($templateName.$this->viewName.$this->getExtension());
}
else if ($this->actionName.$this->viewName == 'newSuccess')
{
  $templateName = sfConfig::get('symfony.view.'.$this->moduleName.'_'.$this->actionName.'_template', $this->actionName);
  $this->setTemplate($templateName.$this->viewName.$this->getExtension());
}
else
{
  $templateName = sfConfig::get('symfony.view.'.$this->moduleName.'_'.$this->actionName.'_template', $this->actionName);
  $this->setTemplate($templateName.$this->viewName.$this->getExtension());
}

if ($templateName.$this->viewName == 'editSuccess')
{
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
  $response->addStylesheet('main.css', '', array ());
  $response->addStylesheet('menu.css', '', array ());
  $response->addStylesheet('/qtip2/dist/jquery.qtip.min.css', '', array ());
  $response->addStylesheet('ui.datepicker.css', '', array ());
  $response->addStylesheet('superfish.css', '', array ());
  $response->addStylesheet('encod.css', '', array ());
  $response->addJavascript('jquery-1.7.1.min.js', '', array ());
  $response->addJavascript('jquery-ui-1.8.17.custom.min.js', '', array ());
  $response->addJavascript('core.js', '', array ());
  $response->addJavascript('/qtip2/dist/jquery.qtip.min.js', '', array ());
  $response->addJavascript('catalogue.js', '', array ());
  $response->addJavascript('superclick.js', '', array ());
}
else if ($templateName.$this->viewName == 'newSuccess')
{
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
  $response->addStylesheet('main.css', '', array ());
  $response->addStylesheet('menu.css', '', array ());
  $response->addStylesheet('/qtip2/dist/jquery.qtip.min.css', '', array ());
  $response->addStylesheet('ui.datepicker.css', '', array ());
  $response->addStylesheet('superfish.css', '', array ());
  $response->addStylesheet('encod.css', '', array ());
  $response->addJavascript('jquery-1.7.1.min.js', '', array ());
  $response->addJavascript('jquery-ui-1.8.17.custom.min.js', '', array ());
  $response->addJavascript('core.js', '', array ());
  $response->addJavascript('/qtip2/dist/jquery.qtip.min.js', '', array ());
  $response->addJavascript('catalogue.js', '', array ());
  $response->addJavascript('superclick.js', '', array ());
}
else
{
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
  $response->addStylesheet('main.css', '', array ());
  $response->addStylesheet('menu.css', '', array ());
  $response->addStylesheet('/qtip2/dist/jquery.qtip.min.css', '', array ());
  $response->addStylesheet('ui.datepicker.css', '', array ());
  $response->addStylesheet('superfish.css', '', array ());
  $response->addJavascript('jquery-1.7.1.min.js', '', array ());
  $response->addJavascript('jquery-ui-1.8.17.custom.min.js', '', array ());
  $response->addJavascript('core.js', '', array ());
  $response->addJavascript('/qtip2/dist/jquery.qtip.min.js', '', array ());
  $response->addJavascript('catalogue.js', '', array ());
  $response->addJavascript('superclick.js', '', array ());
}
