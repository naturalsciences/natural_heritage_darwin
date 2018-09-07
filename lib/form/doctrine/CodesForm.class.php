<?php

/**
 * Codes form.
 *
 * @package    form
 * @subpackage Codes
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class CodesForm extends BaseCodesForm
{
  public function configure()
  {
    $this->useFields(array('code_category', 'code_prefix', 'code_prefix_separator', 'code', 'code_suffix', 'code_suffix_separator'));
    $this->widgetSchema['code_category'] = new sfWidgetFormChoice(array(
        'choices' => Codes::getCategories()
      ));
    $this->validatorSchema['code_category'] = new sfValidatorChoice(array('required' => false, 'choices'=>array_keys(Codes::getCategories())));
    $this->widgetSchema['code_prefix'] = new sfWidgetFormInput();
    $this->widgetSchema['code_prefix']->setAttributes(array('class'=>'lsmall_size'));
    $this->validatorSchema['code_prefix'] = new sfValidatorString(array('required' => false, 'trim'=>true));
    $this->widgetSchema['code_prefix_separator'] = new widgetFormSelectComplete(array(
        'model' => 'Codes',
        'table_method' => 'getDistinctPrefixSep',
        'method' => 'getCodePrefixSeparator',
        'key_method' => 'getCodePrefixSeparator',
        'add_empty' => true,
        'change_label' => '',
        'add_label' => '',
    ));
    $this->widgetSchema['code_prefix_separator']->setAttributes(array('class'=>'vvsmall_size'));
    $this->widgetSchema['code'] = new sfWidgetFormInput();
	  //mrac 2015 06 03 new css class 'mrac_input_mask' for input mask
    $this->widgetSchema['code']->setAttributes(array('class'=>'medium_small_size code_mrac_input_mask'));
    $this->validatorSchema['code'] = new sfValidatorString(array('required' => false, 'trim'=>true));
    $this->widgetSchema['code_suffix'] = new sfWidgetFormInput();
    $this->widgetSchema['code_suffix']->setAttributes(array('class'=>'lsmall_size'));
    $this->validatorSchema['code_suffix'] = new sfValidatorString(array('required' => false, 'trim'=>true));
    $this->widgetSchema['code_suffix_separator'] = new widgetFormSelectComplete(array(
        'model' => 'Codes',
        'table_method' => 'getDistinctSuffixSep',
        'method' => 'getCodeSuffixSeparator',
        'key_method' => 'getCodeSuffixSeparator',
        'add_empty' => true,
        'change_label' => '',
        'add_label' => '',
    ));
    $this->widgetSchema['code_suffix_separator']->setAttributes(array('class'=>'vvsmall_size'));
    $this->mergePostValidator(new CodesValidatorSchema());
    //rmca check unique indexed number 2015 01 13
	$mode_duplicates=sfContext::getInstance()->getUser()->getAttribute("unicity_check_in_session", "off");
	
	//ftheeten 2015 03 11
	$this->colIDSess=sfContext::getInstance()->getUser()->getAttribute("collection_for_insertion", -1);
     if($mode_duplicates=="on")
	{
		$this->mergePostValidator(new sfValidatorCallback(
			array('callback' => array($this, 'setValidatorUniqueNumber'))));
	}
  }
  
      //group below RMCA 2015 01 13

	
	public function setValidatorUniqueNumber($validator, $values, $arguments)
	{
		

		$category=$values["code_category"];
		$prefix=$values["code_prefix"];
		$prefix_sep = $values["code_prefix_separator"];
		$code=$values["code"];
		$suffix=$values["code_suffix"];
		$suffix_sep = $values["code_suffix_separator"];
		
		$old_category = $this->getObject()->getCodeCategory();
		$old_prefix=$this->getObject()->getCodePrefix();
		$old_prefix_sep=$this->getObject()->getCodePrefixSeparator();
		$old_code = $this->getObject()->getCode();
		$old_suffix=$this->getObject()->getCodeSuffix();
		$old_suffix_sep=$this->getObject()->getCodeSuffixSeparator();
		$oldCode=$old_category.$old_prefix.$old_prefix_sep.$old_code.$old_suffix_sep.$old_suffix;
		$newCode=$category.$prefix.$prefix_sep.$code.$suffix_sep.$suffix;
		

		if(sfContext::getInstance()->getActionName()=="create")
		{
			$cpt=$this->getCountCodeIndexedForm($category, $prefix, $prefix_sep, $code, $suffix, $suffix_sep, $this->colIDSess);
			if($cpt>0)
			{
			
				if($this->colIDSess==-1)
				{
					$msgTmp=" in all collections".sfContext::getInstance()->getUser()->getAttribute("collection_for_insertion", -1);
				}
				else
				{
					$collection= Doctrine_Core::getTable('Collections')->find($this->colIDSess);
					$msgTmp=" in collection '".$collection->getName()."'";
				}
			
				throw new sfValidatorError($validator, "Code error: code already exists".$msgTmp);
			}
		}
		
		return $values;
		
		
		
	}
  
	public function getCountCodeIndexed($full_code_indexed)
	{
		$q = Doctrine_Query ::create()->
			select("id")->
			from('Codes')->
			where('full_code_indexed = ?', $full_code_indexed);
			
		
		$r= $q->execute();
		return $r->count();
	}
  
    public function getCountCodeByFields($category, $prefix, $prefix_sep, $code, $suffix, $suffix_sep)
	{
		$q = Doctrine_Query ::create()->
			select("count(id)")->
			from('Codes')->
			where('code_category = ?', $category)->
			andWhere('code_prefix = ?', $prefix)->
			andWhere('code_prefix_separator = ?', $prefix_sep)->
			andWhere('code = ?', $code)->
			andWhere('code_suffix = ?', $suffix)->
			andWhere('code_suffix_separator= ?', $suffix_sep)			
			;
			
		
		return $q->execute(null, Doctrine::HYDRATE_SINGLE_SCALAR);
		
	}
	
	public function getCountCodeIndexedForm($category, $prefix='', $prefix_sep='', $code='', $suffix='', $suffix_sep='',$coll=-1)
	{
	
		
		$searched=$prefix.$prefix_sep.$code.$suffix_sep.$suffix;
		if($coll==-1)
		{
			$q = Doctrine_Query ::create()->
				select("count(id)")->
				from('Codes')->
				where('code_category = ?', $category)->
				andWhere('full_code_indexed  =  fulltoindex(?)', $searched);
			
		}
		else
		{
			 $q=Doctrine_Query::create()->
				select('count(a.id)')->
				from('SpecimensCodes a')->
				innerJoin('a.Specimens b')->
				where('a.code_category = ?', $category)->
				andWhere("TRIM(COALESCE(code_prefix,'')||COALESCE(code_prefix_separator,'')||COALESCE(code,'')||COALESCE(code_suffix_separator,'')||COALESCE(code_suffix,'')) =  ?", $searched)->
				andWhere('b.collection_ref = ?', $coll);
		
		}
		return $q->execute(null, Doctrine::HYDRATE_SINGLE_SCALAR);
		
	}
}
