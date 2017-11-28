<?php


class SpecimensToolsTable extends Doctrine_Table
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('SpecimensTools');
    }
    
    public function getToolName($spec_ref)
    {
    $q = Doctrine_Query::create()
   //   ->select('ct.tool') 
      ->from('SpecimensTools st')      
      ->innerjoin('st.CollectingTools ct')
      ->where('st.specimen_ref = ?',$spec_ref)
      ->orderby('ct.tool_indexed') ;
    return $q->execute() ;
    }    
}
