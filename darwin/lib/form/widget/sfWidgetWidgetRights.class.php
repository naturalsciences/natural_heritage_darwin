<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetDarwinList represents a choice widget for a model.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfWidgetFormDoctrineChoice.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
 
class sfWidgetWidgetRights extends sfWidgetFormChoice
{
  public function configure($options = array(),$attributes = array())
  {
    parent::configure($options, $attributes);  
    $this->addOption('multiple', true);
    $this->addOption('extended', true);     
    $this->addRequiredOption('user_ref');  
    $this->addRequiredOption('collection_ref');      
  }
  
  public function getChoices()
  { 
    $objects = Doctrine::getTable('MyWidgets')->setUserRef($this->getOption('user_ref'))->getAvailableWidgets() ; 
    $choices = array('old_right' => array()) ;
    foreach($objects as $object)
    {
      if(!isset($choices[$object->getCategory()])) $choices[$object->getCategory()] = array() ;
      $choices[$object->getCategory()][$object->getGroupName()] = $object ;
      if(strstr($object->getCollections(),','.$this->getOption('collection_ref').',')) $choices['old_right'][] = $object->getId() ;
    }
    return $choices;
  }
  
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if ($this->getOption('multiple'))
    {
      $attributes['multiple'] = 'multiple';

      if ('[]' != substr($name, -2))
      {
        $name .= '[]';
      }
    }   
    $options = array();
    $choices = $this->getChoices();
    $html = "<tbody>" ;  
	  foreach($choices as $category=>$group) 
	  {
	    if ($category == 'old_right') continue ;
	    $cat = "" ;
    	if($cat != $category) 
	      $html .= "</tbody><tbody>" ;	    
      foreach($group as $group_name=>$widget) 
      {
      	$html .= "<tr>" ;
      	if($cat != $category) 
      	{
      	  $cat = $category ;
    		  $html .= "<th rowspan='".count($group)."' >".$category." <input type='checkbox' id='all_category'></th>" ;     		     
    		}
	      $html .= "<th>".$group_name."</th>" ;		      
	      $html .= "<td><input type=\"checkbox\" value=\"".$widget->getId()."\" name=\"".$name."\" " ;
        if((count($value) && in_array($widget->getId(),$value))||strstr($widget->getCollections(),','.$this->getOption('collection_ref').','))
          $html .= "checked=\"true\" " ;        
        $html .= "></td></tr>" ;
			}			
	  } 
	  return $html ;   
	}
}
