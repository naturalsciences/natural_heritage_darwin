<?php

/**
 * Imports
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Imports extends BaseImports
{
  protected $line_num = 0;
  private static $state = array(
    '' => 'All', 
    'to_be_loaded' => 'To be loaded',    
    'loaded'=>'Loaded',
    'checking'=> 'Checking',
    'pending'=> 'Pending',
    'processing'=> 'Processing',
    'finished' => 'Finished',
    'aborted' => 'Aborted',
    'error' => 'Error',
	//ftheeten 2017 09 13
    'loading' => 'Loading',
  );  
  private static $info = array(
    'to_be_loaded' => 'This file is ready to be loaded, an automatic task will be activated to load lines.',
    'Actif'=> 'The file is actually being loaded in database',
    'loaded'=>'Your file has been loaded, but still need to be checked',
    'checking'=> 'Your file has been loaded and is being checked',
    'pending'=> 'Your file has been loaded and checked, you can edit line in errors or import corrects lines',
    'processing'=> 'ok_line_explanation',
    'finished' => 'This file has been completly been imported in DaRWIN',
    'aborted' => 'This file has been aborted. This line will remain for a limited time in the summary list just for information purposes only.',
    'error' => 'Errors appeared during import, check these errors with the error icon, you can continue the import process or delete the entry and repair your file',
  );

  public static $formatArray = array('abcd' => 'ABCD') ;
  
  public function setCurrentLineNum($nbr)
  {
    $this->line_num = $nbr;
  }
  public function getCurrentLineNum()
  {
    return $this->line_num;
  }

  public static function getFormats()
  {
    return self::$formatArray ;
  }
  
  public function getStateName($name = null)
  {
    if($name) return self::$state[$name];
    if(in_array($this->getState(),array('aloaded','apending','aprocessing'))) return 'Actif' ;
    return self::$state[$this->getState()];
  }
  public static function getStateList()
  {
    return self::$state ;
  } 
   
  public function getStateInfo($state)
  {
    return self::$info[$state];
  }
  
  // function used to determine if we can display edition button or not
  public function isEditableState()
  {
    if($this->getState() == 'error') return true ;
    if($this->getFormat() == 'taxon') return false ;
    if(($this->getState() == 'pending') && ! $this->getIsFinished()) return true ;
    return false ;
  }   

  public function getLastModifiedDate()
  {
    
    $dateTime = new FuzzyDateTime($this->_get('updated_at')!=''?$this->_get('updated_at'):$this->_get('created_at'));
    return $dateTime->getDateMasked('em','d/m/Y H:i');
  }  
  protected function setCollection($collection)
  {
    if($collection == 0) $collection = null ;
    $this->Collection = $collection;
  }
  
  //ftheeten 2017 07 26
  
    public function getCreationDateMasked ()
  {
    $dateTime = new FuzzyDateTime($this->_get('creation_date'), $this->_get('creation_date_mask'));
    return $dateTime->getDateMasked();
  }

  public function getCreationDate()
  {
    $from_date = new FuzzyDateTime($this->_get('creation_date'), $this->_get('creation_date_mask'));
    return $from_date->getDateTimeMaskedAsArray();
  }
  
  
  public function setCreationDate($fd)
  {
    if ($fd instanceof FuzzyDateTime)
    {
      if ($this->getCreationDate() != $fd->getDateTimeMaskedAsArray()) {
        $this->_set('creation_date', $fd->format('Y/m/d'));
        $this->_set('creation_date_mask', $fd->getMask());
      }
    }
    else
    {
      $dateTime = new FuzzyDateTime($fd, 56, true); 
      if ($this->getCreationDate() != $dateTime->getDateTimeMaskedAsArray()) {
        $this->_set('creation_date', $dateTime->format('Y/m/d'));
        $this->_set('creation_date_mask', $dateTime->getMask());
      }
    }
  }
  
  public function getTaxonomicConflicts()
  {
    $returned=Array();
    if($this->getFormat()=="taxon"&& $this->getErrorsInImport()=='taxonomic_conflict')
    {
        $conn = Doctrine_Manager::connection();
        $sql = "SELECT * FROM fct_rmca_compare_taxonomy_staging_darwin(:id) ;";
        $q = $conn->prepare($sql);
		$q->execute(array(':id'=> $this->getId() ));
        $res = $q->fetchAll(PDO::FETCH_ASSOC);

        return $res;
    }
    return $returned;
  
  }
}
