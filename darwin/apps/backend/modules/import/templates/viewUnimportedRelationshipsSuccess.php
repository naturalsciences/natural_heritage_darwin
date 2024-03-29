<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
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

<form action="<?php print(url_for("import/viewUnimportedRelationships"));?>/id/<?php print($id);?>">
Count all : <?php print($size_data);?><br/>
Current page : <?php print($page);?> / <?php print($max_page);?> <br/>
Page : 
<?php if($page>1):?><a href="<?php print(url_for("import/viewUnimportedRelationships"));?>?id=<?php print($id);?>&page=<?php print($page-1);?>"><?php print(__("<"));?></a><?php endif;?>
<select id="page" name="page">
 <?php for($i=0;$i<ceil((int)$size_data/(int)$size_catalogue);$i++):?>
 
 <option <?php print($i+1==$page?'selected="selected"':"");?> value="<?php print($i+1);?>"><?php print($i+1);?></option>
 <?php endfor;?>
</select>
<?php if($page<$max_page):?><a href="<?php print(url_for("import/viewUnimportedRelationships"));?>?id=<?php print($id);?>&page=<?php print($page+1);?>"><?php print(__(">"));?></a><?php endif;?>
<br/>
<input type="submit" value = "go"></submit>
</form>

<table class="staging_table">
<tr>
<th>Message</th>
<th>Count</th>
</tr>
 <?php $sum=0; $i=0; foreach($stats as $stat):?>
    <tr <?php ($stat['import_exception']=="imported_relationship"||$stat['imported'])? print("class='fld_ok'") : print("class='fld_tocomplete'"); ?>>
		<?php $text; $text = $stat['import_exception'] ?: "None" ?>
         <td><a  href="javascript:filter_stats('<?php print($text);?>')" ><?php print($text);?></a></td>
         <td><?php $sum+=(int)$stat['count'];  print($stat['count']);?></td>           
    </tr>
 <?php endforeach;?>
 <tr><td><a href="javascript:reinit_stats();" >Total : </a></td><td><?php print($sum);?></td>
</table>

All data:<br/>
<table class="staging_table">
<tr>
<th>Message</th>
<th>Count</th>
</tr>
 <?php $sum=0; $i=0; foreach($stats_all as $stat):?>
    <tr <?php ($stat['import_exception']=="imported_taxon")? print("class='fld_ok'") : print("class='fld_tocomplete'"); ?>>
		<?php $text; $text = $stat['import_exception'] ?: "None" ?>
         <td><a  href="javascript:filter_stats('<?php print($text);?>')" ><?php print($text);?></a></td>
         <td><?php $sum+=(int)$stat['count'];  print($stat['count']);?></td>           
    </tr>
 <?php endforeach;?>
 <tr><td><a href="javascript:reinit_stats();" >Total : </a></td><td><?php print($sum);?></td>
</table>

<br/>


<a href="<?php print(url_for("import/importrelationships"));?>?id=<?php print($id);?>"><?php print(__("import"));?></a>
<br/>
<br/>
<table id="result_taxa" name="result_taxa" class="staging_table catalogue_table edition" style="max-width:100%; table-layout: fixed;word-wrap: break-word;" >
 <tr > 
        <th>id</th>
		<th>status</th>
		<th>relationship_type</th>
		<th>specimen_submittted_ref</th>
        <th>specimen_main_uuid</th>
        <th>specimen_main_code</th>
        <th >specimen Foreign Key (main code)</th>
		<th>related_specimen_submittted_ref</th>
        <th>related_specimen_main_uuid</th>
        <th>related_specimen_main_code</th>
        <th>related specimen  Foreign Key (main code)</th>
        <th>Imported</th>
		
 </tr>
 <tbody id="taxon_body">
 <?php $i=0; foreach($items as $item):?>
	<?php
				$go_valid_1=true;
				if(strlen(trim($item['specimen_ref']))==0)
				{
					if(!is_numeric($item['specimen_ref']))
					{
						$go_valid_1=false;
					}
				}
				
				$go_valid_2=true;
				if(strlen(trim($item['specimen_related_ref']))==0)
				{
					if(!is_numeric($item['specimen_related_ref']))
					{
						$go_valid_2=false;
					}
				}
				
			?>
    <tr <?php ( $item['imported'])? print("class='fld_ok'") : print("class='fld_tocomplete'"); ?>>
		<?php $text; $text = $item['import_exception'] ?: "None" ?>
         <td><?php print($item['id']);?></td>
		 <td><?php print($item['status_str']);?></td>
		 <td><?php print($item['relationship_type']);?></td>
		 <td><?php print($item['specimen_submitted_ref']);?></td>
		 <td><?php print($item['specimen_uuid']);?></td>
		 <td><?php print($item['specimen_main_code']);?></td>
         <td>
			<?php if($go_valid_1):?>
				<?php print($item['specimen_ref']);?>
			<?php else:?>
				<input type="text" id="specimen_ref_<?php print($i);?>" name="specimen_ref_<?php print($i);?>" class="autocomplete_for_code" style="max-width:90%"/>
				<input type="hidden" id="catch_specimen_ref_<?php print($i);?>" name="catch_specimen_ref_<?php print($i);?>"/>
				<input type="hidden" id="imp_id_specimen_ref_<?php print($i);?>" name="imp_id_specimen_ref_<?php print($i);?>" value="<?php print($item['id']);?>"/>
				<button type="button" id="save_specimen_ref_<?php print($i);?>" name="save_specimen_ref_<?php print($i);?>" style="background-color:#e7e7e7;" class="save_specimen_ref">Save</button>
			<?php endif;?>
		 </td> 
		 
		 <td><?php print($item['specimen_related_submitted_ref']);?></td>
		 <td><?php print($item['specimen_related_uuid']);?></td>
		 <td><?php print($item['specimen_related_main_code']);?></td>
         <td>
			<?php if($go_valid_2):?>
				<?php print($item['specimen_related_ref']);?>
			<?php else:?>
				<input type="text" id="specimen_related_ref_<?php print($i);?>" name="specimen_related_ref_<?php print($i);?>" class="autocomplete_for_code"  style="max-width:90%"/>
				<input type="hidden" id="catch_specimen_related_ref_<?php print($i);?>" name="catch_specimen_related_ref_<?php print($i);?>"/>
				<input type="hidden" id="imp_id_specimen_related_ref_<?php print($i);?>" name="imp_id_specimen_related_ref_<?php print($i);?>" value="<?php print($item['id']);?>"/>
				<button type="button" id="save_specimen_related_ref_<?php print($i);?>" name="save_specimen_related_ref_<?php print($i);?>" style="background-color:#e7e7e7;" class="save_specimen_related_ref">Save</button>
			<?php endif;?>
		 </td> 
		  <td><?php print($item['imported']?"true":"false");?></td>
		   
                      
    </tr>
	<?php $i++;?>
 <?php endforeach;?>   
 </tbody>
</table>

<br>

<script>

var url="<?php echo(url_for('catalogue/codesTaxonAutocompleteForLoans?'));?>";

				$(".save_specimen_ref").click(
					function()
					{
						var id=$(this).attr("id");
						var corresponding=id.replace("save_", "catch_");
						var tmp=$("#"+corresponding).val();
						var corresponding_imp=id.replace("save_", "imp_id_");
						var tmp_imp=$("#"+corresponding_imp).val();
						
						if(tmp.length>0)
						{
							
							
							 $.getJSON( "<?php print(url_for("import/updateStagingRelationship"));?>", {stag_id: tmp_imp, spec_id:tmp, field_id:"main"}, function( result ) {
							 console.log(result);
							 
							});
							
						}
					}
				);
				
				$(".save_specimen_related_ref").click(
					function()
					{
						var id=$(this).attr("id");
						var corresponding=id.replace("save_", "catch_");
						var tmp=$("#"+corresponding).val();
						var corresponding_imp=id.replace("save_", "imp_id_");
						var tmp_imp=$("#"+corresponding_imp).val();
						
						if(tmp.length>0)
						{
							
							
							 $.getJSON( "<?php print(url_for("import/updateStagingRelationship"));?>", {stag_id: tmp_imp, spec_id:tmp, field_id:"related"}, function( result ) {
							 console.log(result);
							 
							});
							
						}
					}
				);
				
				$(".autocomplete_for_code").autocomplete({
     
					source: function (request, response) {
						$.getJSON(url, {
									term : request.term
								} , 
								function (data) 
									{
								response($.map(data, function (value, key) {
								return value;
								}));
						});
					},
					select: function (event, ui) {
							var id=$(this).attr("id");
							var corresponding="catch_"+id;
							$("#"+corresponding).val(ui.item.id)        
					},
					minLength: 2,
					delay: 100
				});
			

function filter_stats(input ) 
{
  // Declare variables
  var  filter, table, tr, td, i, txtValue;
  filter = input.toUpperCase();
  table = document.getElementById("result_taxa");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) 
  {
    td = tr[i].getElementsByTagName("td")[5];
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
  
  var table = document.getElementById("result_taxa");
  var tr = table.getElementsByTagName("tr");
  var i;
  for (i = 0; i < tr.length; i++) {

        tr[i].style.display = "";
    }
}
</script>