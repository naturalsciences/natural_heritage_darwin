<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Codes extends BaseCodes
{
  private static $category = array('main'=> 'Main',
                 'secondary' => 'Second.',
                 'temporary' => 'Temp.',
                 'inventory'=> 'Invent.',
                 'barcode' => 'Barcode',
                 'additional id' => 'Additional',
                 'code' => 'Storage',
                 'genbank number' => 'GenBank Nb.',
				 'storage id' => 'Storage ID'
                );

  public static function getCategories()
  {
    try{
        $i18n_object = sfContext::getInstance()->getI18n();
    }
    catch( Exception $e )
    {
        return self::$category;
    }
    return array_map(array($i18n_object, '__'), self::$category);
  }  
  
  public function getCodeFormated()
  {
    $code_prefix = $this->_get('code_prefix');
    $code_prefix_separator = (strlen($this->_get('code_prefix_separator')))?$this->_get('code_prefix_separator'):' ';
    $code = (strlen($this->_get('code')))?$this->_get('code'):'-';
    $code_suffix = $this->_get('code_suffix');
    $code_suffix_separator = (strlen($this->_get('code_suffix_separator')))?$this->_get('code_suffix_separator'):' ';

    if (strlen($code_prefix))
      $code_prefix .= $code_prefix_separator;

    if (strlen($code_suffix))
      $code_suffix = $code_suffix_separator.$code_suffix;

    return $code_prefix.$code.$code_suffix;
  }
}
