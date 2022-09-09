<style>
table, th, td {
   border: 1px solid black;
}

th, td {
    padding: 15px;
    text-align: left;
}



</style>
<div class="page">
<div>

<form action="<?php print(url_for("import/viewUnimportedGtu"));?>/id/<?php print($id);?>">
Count all : <?php print($size_data);?><br/>
Current page : <?php print($page);?> / <?php print($max_page);?> <br/>
Page : 
<?php if($page>1):?><a href="<?php print(url_for("import/viewUnimportedGtu"));?>?id=<?php print($id);?>&page=<?php print($page-1);?>"><?php print(__("<"));?></a><?php endif;?>
<select id="page" name="page">
 <?php for($i=0;$i<ceil((int)$size_data/(int)$size_catalogue);$i++):?>
 
 <option <?php print($i+1==$page?'selected="selected"':"");?> value="<?php print($i+1);?>"><?php print($i+1);?></option>
 <?php endfor;?>
</select>
<?php if($page<$max_page):?><a href="<?php print(url_for("import/viewUnimportedGtu"));?>?id=<?php print($id);?>&page=<?php print($page+1);?>"><?php print(__(">"));?></a><?php endif;?>
<br/>
<input type="submit" value = "go"></submit>
</form>

<table class="staging_table">
<tr >
<th>Message</th>
<th>Count</th>
</tr>
 <?php $sum=0; $i=0; foreach($stats as $stat):?>
    <tr <?php ($stat['import_exception']=="imported"||$stat['import_exception']=="imported.imported")? print("class='fld_ok'") : print("class='fld_tocomplete'"); ?> >
		<?php $text; $text = $stat['import_exception'] ?: "None" ?>
         <td><a  href="javascript:filter_stats('<?php print($text);?>')" ><?php print($text);?></a></td>
         <td><?php $sum+=(int)$stat['count'];  print($stat['count']);?></td>           
    </tr>
 <?php endforeach;?>
 <tr><td><a href="javascript:reinit_stats();" >Total : </a></td><td><?php print($sum);?></td>
</table>

All data:<br/>
<table class="staging_table">
<tr >
<th>Message</th>
<th>Count</th>
</tr>
 <?php $sum=0; $i=0; foreach($stats_all as $stat):?>
    <tr <?php ($stat['import_exception']=="imported"||$stat['import_exception']=="imported.imported")? print("class='fld_ok'") : print("class='fld_tocomplete'"); ?>>
		<?php $text; $text = $stat['import_exception'] ?: "None" ?>
         <td><a  href="javascript:filter_stats('<?php print($text);?>')" ><?php print($text);?></a></td>
         <td><?php $sum+=(int)$stat['count'];  print($stat['count']);?></td>           
    </tr>
 <?php endforeach;?>
 <tr><td><a href="javascript:reinit_stats();" >Total : </a></td><td><?php print($sum);?></td>
</table>
<br/>
<br/>

<table id="result_gtu" name="result_gtu" class="staging_table" >
 <tr>   
        <th>id_file</th>
		<th>id_staging_gtu_db</th>  
		<th>imported</th>
        <th>import_exception</th>
		<th>status</th>     
        <th>gtu_ref</th>
        <th>operation</th>
        <th>station_type</th>
        <th>sampling_code</th>
        <th>sampling_field_number</th>
		<th>tags</th>
        <th>event_cluster_code</th>
        <th>event_order</th>
        <th>ig_num</th>
        <th>ig_num_indexed</th>
        <th>collections</th>
        <th>collectors</th>
        <th>expeditions</th>
        <th>collection_refs</th>
        <th>collector_refs</th>
        <th>expedition_refs</th>
        <th>iso3166</th>
        <th>iso3166_subdivision</th>
        <th>countries</th>
        <th>tags</th>
        <th>tags_indexed</th>
        <th>locality_text</th>
        <th>locality_text_indexed</th>
        <th>ecology_text</th>
        <th>ecology_text_indexed</th>
        <th>coordinates_format</th>
        <th>latitude1</th>
        <th>longitude1</th>
        <th>latitude2</th>
        <th>longitude2</th>
        <th>gis_type</th>
        <th>coordinates_wkt</th>
        <th>coordinates_datum</th>
        <th>coordinates_proj_ref</th>
        <th>coordinates_original</th>
        <th>coordinates_accuracy</th>
        <th>coordinates_accuracy_text</th>
        <th>station_baseline_elevation</th>
        <th>station_baseline_accuracy</th>
        <th>sampling_elevation_start</th>
        <th>sampling_elevation_end</th>
        <th>sampling_elevation_accuracy</th>
        <th>original_elevation_data</th>
        <th>sampling_depth_start</th>
        <th>sampling_depth_end</th>
        <th>sampling_depth_accuracy</th>
        <th>original_depth_data</th>
        <th>collecting_date_begin</th>
        <th>collecting_date_begin_mask</th>
        <th>collecting_date_end</th>
        <th>collecting_date_end_mask</th>
        <th>collecting_time_begin</th>
        <th>collecting_time_end</th>
        <th>sampling_method</th>
        <th>sampling_fixation</th>
        <th>the_geom</th>
        <th>imported</th>
        
      </tr>  
 <?php $i=0; foreach($items as $item):?>
    <tr <?php ($item['import_exception']=="imported"||$item['import_exception']=="imported.imported")? print("class='fld_ok'") : print("class='fld_tocomplete'"); ?>>
        <td><?php print($item['pos_in_file']);?></td>
        <td><?php print($item['id']);?></td> 
		<td><?php print($item['imported']? "YES" : "NO");?></td>
        <td><?php print($item['import_exception']);?></td>
		<td><?php foreach( hstore2array($item['status']) as $key=>$var):?>
                <?php if($key=="gtu_id"): ?>
                    <?php print($key.' : ');?><br/>
                    <?php foreach(explode(";", $var) as $gtu_id):?>
                       <?php print(link_to($gtu_id, "gtu/view?id=$gtu_id", array("target"=>"_blank")));?><br/>
                     <?php endforeach;?>
                <?php else: ?>
                    <?php print($key.' : '.$var);?><br/>
                 <?php endif; ?>
            <?php endforeach;?>
        </td>		
        <td>
            <?php if(strlen($item["gtu_ref"])>0): ?>
                <?php print(link_to($item["gtu_ref"], "gtu/view?id=".$item["gtu_ref"], array("target"=>"_blank")));?>
            <?php endif; ?>
        </td>
        <td>          
            <?php if(strpos($item['import_exception'],"duplicate_code")!==FALSE || strpos($item['import_exception'],"code_already_in_file_with_other_data")!==FALSE):?>
                           
            <?php endif;?>       
        </td>
        <td><?php print($item['station_type']);?></td>
        <td>
            <?php if(strpos($item['import_exception'],"duplicate_code")!==FALSE || strpos($item['import_exception'],"code_already_in_file_with_other_data")!==FALSE):?>
                    <?php print(form_tag('import/changeStagingGtuCode', array("method"=>"GET") ));?>
                    <input type="hidden" id="input_gtu_id_<?php print($item['id']);?>" name="staging_gtu_id" value="<?php print($item['id']);?>" />
                    <input id="input_gtu_code_<?php print($item['id']);?>" name="sampling_code" class="editable_gtu_code"  style="width:250px" type="texte" value="<?php print($item['sampling_code']);?>"></input>
                    <br/>
                    <input type="submit"  value="change code and import" />    
                    </form>
					 <?php print(form_tag('import/loadSingleGtuInDB', array("method"=>"GET") ));?>
                    <input type="hidden" id="input_gtu_id_<?php print($item['id']);?>" name="staging_gtu_id" value="<?php print($item['id']);?>" />                    
                    <input type="submit"  value="Force import with current code" />    
                    </form>    
            <?php else:?>
                <?php print($item['sampling_code']);?></td>
            <?php endif;?>
        <td><?php print($item['sampling_field_number']);?></td>
		<td><?php print($item['tag_values']);?></td>
        <td><?php print($item['event_cluster_code']);?></td>
        <td><?php print($item['event_order']);?></td>
        <td><?php print($item['ig_num']);?></td>
        <td><?php print($item['ig_num_indexed']);?></td>
        <td><?php print($item['collections']);?></td>
        <td><?php print($item['collectors']);?></td>
        <td><?php print($item['expeditions']);?></td>
        <td><?php print($item['collection_refs']);?></td>
        <td><?php print($item['collector_refs']);?></td>
        <td><?php print($item['expedition_refs']);?></td>
        <td><?php print($item['iso3166']);?></td>
        <td><?php print($item['iso3166_subdivision']);?></td>
        <td><?php print($item['countries']);?></td>
        <td><?php print($item['tags']);?></td>
        <td><?php print($item['tags_indexed']);?></td>
        <td><?php print($item['locality_text']);?></td>
        <td><?php print($item['locality_text_indexed']);?></td>
        <td><?php print($item['ecology_text']);?></td>
        <td><?php print($item['ecology_text_indexed']);?></td>
        <td><?php print($item['coordinates_format']);?></td>
        <td><?php print($item['latitude1']);?></td>
        <td><?php print($item['longitude1']);?></td>
        <td><?php print($item['latitude2']);?></td>
        <td><?php print($item['longitude2']);?></td>
        <td><?php print($item['gis_type']);?></td>
        <td><?php print($item['coordinates_wkt']);?></td>
        <td><?php print($item['coordinates_datum']);?></td>
        <td><?php print($item['coordinates_proj_ref']);?></td>
        <td><?php print($item['coordinates_original']);?></td>
        <td><?php print($item['coordinates_accuracy']);?></td>
        <td><?php print($item['coordinates_accuracy_text']);?></td>
        <td><?php print($item['station_baseline_elevation']);?></td>
        <td><?php print($item['station_baseline_accuracy']);?></td>
        <td><?php print($item['sampling_elevation_start']);?></td>
        <td><?php print($item['sampling_elevation_end']);?></td>
        <td><?php print($item['sampling_elevation_accuracy']);?></td>
        <td><?php print($item['original_elevation_data']);?></td>
        <td><?php print($item['sampling_depth_start']);?></td>
        <td><?php print($item['sampling_depth_end']);?></td>
        <td><?php print($item['sampling_depth_accuracy']);?></td>
        <td><?php print($item['original_depth_data']);?></td>
        <td><?php print($item['collecting_date_begin']);?></td>
        <td><?php print($item['collecting_date_begin_mask']);?></td>
        <td><?php print($item['collecting_date_end']);?></td>
        <td><?php print($item['collecting_date_end_mask']);?></td>
        <td><?php print($item['collecting_time_begin']);?></td>
        <td><?php print($item['collecting_time_end']);?></td>
        <td><?php print($item['sampling_method']);?></td>
        <td><?php print($item['sampling_fixation']);?></td>
        <td><?php print($item['the_geom']);?></td>
        <td><?php print($item['imported']);?></td>
        
      </tr>  
 <?php endforeach;?>
</table>
<a href="../../indexLocalities">Back to import list</a>
</div>
</div>

<script>
function filter_stats(input ) 
{
  // Declare variables
  var  filter, table, tr, td, i, txtValue;
  filter = input.toUpperCase();
  table = document.getElementById("result_gtu");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) 
  {
    td = tr[i].getElementsByTagName("td")[3];
    if (td) 
	{
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } 
	  else 
	  {
		  
        tr[i].style.display = "none";
      }
    }
  }
}

function reinit_stats( ) 
{
  
  var table = document.getElementById("result_gtu");
  var tr = table.getElementsByTagName("tr");
  var i;
  for (i = 0; i < tr.length; i++) {

        tr[i].style.display = "";
    }
}


</script>