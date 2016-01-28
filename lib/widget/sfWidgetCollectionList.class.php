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
 
class sfWidgetCollectionList extends sfWidgetFormChoice
{
  public function configure($options = array(),$attributes = array())
  {
    parent::configure($options, $attributes);
    $this->addOption('multiple', true);
    $this->addOption('extended', true);
    $this->addOption('is_choose',false);
  }

  private function getCollectionByIntitution()
  {
    $tab = array() ;
    $user=null;
    $only_public = true;
    if(! $this->hasOption('public_only') || $this->getOption('public_only') === false )
    {
      $user = sfContext::getInstance()->getUser();
      $only_public = false;
    }
    $objects = Doctrine_Core::getTable('Collections')->fetchByInstitutionList($user, null, $only_public) ;  
    foreach($objects as $institution)
    {
      $tab[$institution->getFormatedName()] = array() ;
      foreach($institution->Collections as $collection)
       $tab[$institution->getFormatedName()][] = $collection ;
    } 
    return $tab ;
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

    $institutions = $this->getCollectionByIntitution() ;
    $html = "" ;
    $prev_level = 0 ;
    foreach($institutions as $institution=> $collections)
    {

      $root = $tree = new Collections();
      foreach($collections as $item)
      {
        $anc = $tree->getFirstCommonAncestor($item);
        $anc->addChild($item);
        $tree = $item;
      }

      $html .= "<h2>$institution</h2>" ;
      $html .= "<div class=\"treelist\">" ;

      $html .= $this->displayTree($root,'', $value, $name);

      $html .= "</div>" ;
    }
    return $html;
  }
  

  function displayTree(Collections $elem, $html, $value, $name, $user=null)
  {
    $img_expand = 'blue_expand.png';
    $img_expand_up = 'blue_expand_up.png' ;

    if($elem->hasChild())
    {
      $html .= '<ul>';
      foreach( $elem->getChilds() as $child)
      {
        $html .= "<li class=\"rid_".$child->getId()."\"";
        if($child->isEncodable())
          $html .= ' data-enc="true" ';
        $html .= "><div class=\"col_name\">" ;
        $html .= image_tag ($img_expand, array('alt' => '+', 'class'=> 'tree_cmd collapsed'));
        $html .= image_tag ($img_expand_up, array('alt' => '-', 'class'=> 'tree_cmd expanded hidden'));
        $html .=  "<span>".$child->getName()."</span>";

        $options = array(
          'type'=> 'checkbox',
          'class' => 'col_check',
          'value' => $child->getId(),
          'name' => $name,
        );
        if(is_array($value) && in_array($child->getId(), $value) )
          $options['checked'] = "checked";

        if($name != '')
          $html .= '<label class="chk">'.$this->renderTag('input', $options).'</label>';//.'<b class="clear">&nbsp;</b>';
        else
        {
          if(! $this->getOption('is_choose') )
          {
            $html .= ' '.image_tag('info.png',array('title'=>'info','class'=>'extd_info','data-manid'=> $child->getMainManagerRef(), 'data-staffid'=> $child->getStaffRef()));
            if($user->isA(Users::ADMIN) || ( $user->isAtLeast(Users::MANAGER) && $child->getTypeInCol() >= Users::MANAGER  ) )
            {
             $html .= link_to(image_tag('edit.png',array('title'=>'Edit Collection','class' => 'collection_edit')),'collection/edit?id='.$child->getId());
             $html .= link_to(image_tag('duplicate.png',array('title'=>'Duplicate Collection')),'collection/new?duplicate_id='.$child->getId());
            }
          }
        }

        $html .= "</div>" ;
        $html .= $this->displayTree($child,'', $value, $name, $user).'</li>';
      }
      $html .= '</ul>';
    }
    return  $html;
  }
}
