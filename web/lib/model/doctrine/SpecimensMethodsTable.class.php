<?php


class SpecimensMethodsTable extends Doctrine_Table
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('SpecimensMethods');
    }
    
    public function getMethodName($spec_ref)
    {
    $q = Doctrine_Query::create()
//      ->select('cm.method, sm.specimen_ref') 
      ->from('SpecimensMethods sm')      
      ->innerjoin('sm.CollectingMethods cm')
      ->where('sm.specimen_ref = ?',$spec_ref)
      ->orderby('cm.method_indexed') ;
    return $q->execute() ;
    }
}
