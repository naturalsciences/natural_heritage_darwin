<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class GtuTable extends DarwinTable
{

      //ftheeten 2018 12 12
  public function getRelatedTemporalInformationMaskedGtuId($p_gtu_id)
  {
	  $returned=Array();
	  
      foreach(Doctrine_Core::getTable('TemporalInformation')->getDistinctTemporalInformation($p_gtu_id) as $key=>$array)
      {
            $item = new TemporalInformation();
            $item->fromArray($array);
		    if((int)$item->getFromDateMask()>0||(int)$item->getToDateMask()>0)
			{
				$tmp=Array();
                $tmp['id']=$item->getId();
                $tmp['from_raw']=$item->getFromDate();
				$tmp['to_raw']=$item->getToDate();
				$tmp["from"]=$item->getFromDateString();//getFromDateMasked(ESC_RAW);
				$tmp["to"]=$item->getToDateString(); //getToDateMasked(ESC_RAW);
                $tmp["from_masked"]=$item->getFromDateMasked(ESC_RAW);
                $tmp["to_masked"]=$item->getToDateMasked(ESC_RAW);
                $tmp["from_mask"]=(int)$item->getFromDateMask();
                $tmp["to_mask"]=(int)$item->getToDateMask();
                
                $tmp['from_year']=$item->getFromDate()['year'];
                $tmp['from_month']=$item->getFromDate()['month'];
                $tmp['from_day']=$item->getFromDate()['day'];
                $tmp['from_hour']=$item->getFromDate()['hour'];
                $tmp['from_minute']=$item->getFromDate()['minute'];
                $tmp['from_second']=$item->getFromDate()['second'];
                $tmp['to_year']=$item->getToDate()['year'];
                $tmp['to_month']=$item->getToDate()['month'];
                $tmp['to_day']=$item->getToDate()['day'];
                $tmp['to_hour']=$item->getToDate()['hour'];
                $tmp['to_minute']=$item->getToDate()['minute'];
                $tmp['to_second']=$item->getToDate()['second'];
                

				$returned[]=$tmp;
			}
	  }
    
	  return $returned;
  }
  
  public function callTranslateService($word)
  {
	  $rows=array();
	  if(strlen($word)>1)
	  {
		   $conn_MGR = Doctrine_Manager::connection();
           $conn = $conn_MGR->getDbh();
           $query="SELECT DISTINCT  '' AS source_table, '' AS wikidata, '' AS reference_name, translated_name,  
       string_agg(DISTINCT lang_iso,';') as lang_iso, LENGTH(translated_name) FROM darwin2.rmca_wfs_translation_service_2(:tag) GROUP BY translated_name ORDER BY translated_name, string_agg(DISTINCT lang_iso,';'), LENGTH(translated_name);";
		   $stmt=$conn->prepare($query);
           $stmt->bindValue(":tag", $word);
		   $stmt->execute();
		   $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
       
         if(count($rs)>0)
         {
              return $rs;
         }
          
            
	  }
	  return $rows;
  }
  
    public function callTranslateServiceWfsGeometry($table, $ids)
  {
	  $ids="{".$ids."}";
	  $rows=array();
	  if(strlen($table)>1&&strlen($ids))
	  {
		   $conn_MGR = Doctrine_Manager::connection();
           $conn = $conn_MGR->getDbh();
           $query="SELECT * FROM rmca_wfs_get_darwin_translations(:table_name, :ids);";
		   $stmt=$conn->prepare($query);
           $stmt->bindValue(":table_name", $table);
		   $stmt->bindValue(":ids", $ids);
		   $stmt->execute();
		   $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
       
         if(count($rs)>0)
         {
              return $rs;
         }
          
            
	  }
	  return $rows;
  }
  
}
