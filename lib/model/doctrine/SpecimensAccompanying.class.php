<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SpecimensAccompanying extends BaseSpecimensAccompanying
{
  private static $accompanying = array('biological'=> 'Biological', 'mineral'=> 'Mineral');
  
  public static function getAccompanyingTypes()
  {
    try{
        $i18n_object = sfContext::getInstance()->getI18n();
    }
    catch( Exception $e )
    {
        return self::$accompanying;
    }
    return array_map(array($i18n_object, '__'), self::$accompanying);
  } 
}
