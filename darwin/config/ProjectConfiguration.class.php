<?php

require_once dirname(__FILE__).'/../lib/vendor/symfony/lib/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    // for compatibility / remove and enable only the plugins you want
    $this->enableAllPluginsExcept(array('sfPropelPlugin'));  
    //ftheeten lexpress modification
	//sfConfig::set('sf_upload_dir', sfConfig::get('sf_root_dir').DIRECTORY_SEPARATOR.'uploads');
	$this->dispatcher->connect('doctrine.configure', array($this, 'configureDoctrineEvent'));
	$this->dispatcher->connect(
     'doctrine.filter_model_builder_options', 
     array($this, 'configureDoctrineBuildOptions')
   );
  }

 /**
  * Configure the Doctrine engine
  **/
  /*public function configureDoctrine(Doctrine_Manager $manager) {
    $manager->setAttribute(Doctrine::ATTR_QUERY_CACHE, new Doctrine_Cache_Apc());
    $manager->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE, new Doctrine_Cache_Apc());
    $options = array('baseClassName' => 'DarwinModel');
    sfConfig::set('doctrine_model_builder_options', $options);
  }*/
  
  //specific for lexpress PHP 7 fork
  public function configureDoctrineEvent(sfEvent $event)
  {
	$manager = $event->getSubject();

    $manager->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE, new Doctrine_Cache_Apc());
    $manager->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE, new Doctrine_Cache_Apc());
    //$options = array('baseClassName' => 'DarwinModel');
    //sfConfig::set('doctrine_model_builder_options', $options);
	//return $options;
  }
  
	  public function configureDoctrineBuildOptions(sfEvent $event, $options)
	{
	   $options['baseClassName'] = 'DarwinModel';

	   return $options;
	}
}
