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

<form action="<?php print(url_for("import/viewUnimportedSynonymies"));?>/id/<?php print($id);?>">
Count all : <?php print($size_data);?><br/>
Current page : <?php print($page);?> / <?php print($max_page);?> <br/>
Page : 
<?php if($page>1):?><a href="<?php print(url_for("import/viewUnimportedSynonymies"));?>?id=<?php print($id);?>&page=<?php print($page-1);?>"><?php print(__("<"));?></a><?php endif;?>
<select id="page" name="page">
 <?php for($i=0;$i<ceil((int)$size_data/(int)$size_catalogue);$i++):?>
 
 <option <?php print($i+1==$page?'selected="selected"':"");?> value="<?php print($i+1);?>"><?php print($i+1);?></option>
 <?php endfor;?>
</select>
<?php if($page<$max_page):?><a href="<?php print(url_for("import/viewUnimportedSynonymies"));?>?id=<?php print($id);?>&page=<?php print($page+1);?>"><?php print(__(">"));?></a><?php endif;?>
<br/>
<input type="submit" value = "go"></submit>
</form>

<table class="staging_table">
<tr>
<th>Message</th>
<th>Count</th>
</tr>
 <?php $sum=0; $i=0; foreach($stats as $stat):?>
    <tr <?php ($stat['import_exception']=="imported_taxon"||$stat['imported'])? print("class='fld_ok'") : print("class='fld_tocomplete'"); ?>>
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


<a href="<?php print(url_for("import/importsynonyms"));?>?id=<?php print($id);?>"><?php print(__("import"));?></a>
<br/>
<br/>
<table id="result_taxa" name="result_taxa" class="staging_table catalogue_table edition">
 <tr > 
        <th>id</th>
        <th>status</th>
        <th>valid_name</th>
        <th>valid_name_ref</th>
		<th>action_valid_name</th>
        <th>synonym</th>
        <th>synonym_ref</th>
        <th>action_synonym</th>
        <th>Imported</th>
		
 </tr>
 <tbody id="taxon_body">
 <?php $i=0; foreach($items as $item):?>
    <tr <?php ($item['import_exception']=="imported_taxon"|| $item['imported'])? print("class='fld_ok'") : print("class='fld_tocomplete'"); ?>>
		<?php $text; $text = $item['import_exception'] ?: "None" ?>
         <td><?php print($item['id']);?></td>
		 <td><?php print($item['status_str']);?></td>
		 <td id="td_valid_name_text_<?php print($i);?>" ><?php print($item['valid_name']);?></td>
		 
			<?php
				$go_valid=true;
				if(strlen(trim($item['valid_name_ref']))==0||strpos(strtolower($item['status_str']), "merge") !== false)
				{
					if(!is_numeric($item['valid_name_ref']))
					{
						$go_valid=false;
					}
				}
				
			?>
			
			<td id="td_valid_name_ref_<?php print($i);?>">
			<?php if($go_valid):?>
				<?php print($item['valid_name_ref']);?>
			<?php endif;?>
			</td>
			<td>
			<?php if(!$go_valid): ?>
				
				<a  id="link_valid_<?php print($i);?>" class="add_taxa button_taxa" href="<?php print(url_for("taxonomy/choose"));?>?with_js=1&name=<?php  print(urlencode($item['valid_name']));?>&taxonomy=<?php  print($import->getSpecimenTaxonomyRef()); ?>" data-type-name="valid" data-synrow-id="<?php print $item["id"];?>" ><?php echo __('Search Taxon...');?></a>
			<?php endif; ?>
		 </td>
		 <td id="td_syn_name_text_<?php print($i);?>" ><?php print($item['syn_name']);?></td>
		 
			<?php
				$go_valid=true;
				if(strlen(trim($item['syn_ref']))==0)
				{
					if(!is_numeric($item['syn_ref']))
					{
						$go_valid=false;
					}
				}
				
			?>
			<td id="td_syn_name_ref_<?php print($i);?>">
			
			<?php if($go_valid):?>
				<?php print($item['syn_ref']);?>
			<?php endif ?>
			</td>
			<td>
			<?php if(!$go_valid): ?>
				<a  id="link_syno_<?php print($i);?>" class="add_taxa button_taxa" href="<?php print(url_for("taxonomy/choose"));?>?with_js=1&name=<?php  print(urlencode($item['syn_name']));?>&taxonomy=<?php  print($import->getSpecimenTaxonomyRef()); ?>" data-type-name="synonym" data-synrow-id="<?php print $item["id"];?>" ><?php echo __('Search Taxon...');?></a>
			<?php endif; ?>
		 
		 </td>
         
          
		  <td><?php print($item['imported']?"true":"false");?></td>
		   
                      
    </tr>
	<?php $i++;?>
 <?php endforeach;?>   
 </tbody>
</table>

<br>

<script>
var current_type_name=null;
var current_row_id=null;
var current_ctrl_id=null;

function addTaxon(people_ref, people_name)
		{ 
			
		
		  hideForRefresh($('.ui-tooltip-content .page')) ; 
		  $.ajax(
		  {
			type: "GET",
			url: "<?php print(url_for("taxonomy/choose"));?>?with_js=1",
			success: function(html)
			{
			
			  $('#taxon_body').append(html);
			  $.fn.catalogue_people.reorder($('#result_taxa'));
			  showAfterRefresh($('.ui-tooltip-content .page')) ; 
			}
		  }); 
		   
		   $('body').trigger('close_modal');
		  return true;
		}

$(document).ready(
	function()
	{
		//$(".modal_taxa").click(button_ref_modal);
		
		$(".add_taxa").click(
			function()
			{
					
				console.log($(this).attr("id"));
				current_type_name=$(this).attr("data-type-name");
				current_row_id=$(this).attr("data-synrow-id");
				current_ctrl_id=$(this).attr("id");
				
				
			}
		);
		
		$("#result_taxa").catalogue_people({handle: '.sender_table_handle', add_button: '#result_taxa a.add_taxa', q_tip_text: '<?php echo __('Add Sender');?>',update_row_fct: addTaxon });
		
	
		
		
		
		onElementInserted('body', '.result_choose',
			function(elem)
			{
				$(elem).click(
					function()
					{
						console.log("CLOSE");
						console.log(current_type_name);
						console.log(current_row_id);
						
						var tech_id=current_ctrl_id.split("_")
						tech_id=tech_id[tech_id.length-1];
						var name_id=$(elem).attr("data-item-id");
						var name_str=$(elem).attr("data-item-name");
						console.log(name_id);
						$('body').trigger('close_modal');
						
						 $.getJSON( "<?php print(url_for("import/updateStagingSyn"));?>", {current_type_name: current_type_name, current_row_id:current_row_id,name_id:name_id, name:name_str}, function( result ) {
							 console.log(result);
							 if(current_type_name=="synonym")
							 {
								 $("#td_syn_name_ref_"+tech_id).html(name_id);
								 $("#td_syn_name_text_"+tech_id).html(name_str);
							 }
							 else if(current_type_name=="valid")
							 {
								 $("#td_valid_name_ref_"+tech_id).html(name_id);
								 $("#td_valid_name_text_"+tech_id).html(name_str);
							 }
							});
					}
				
				);
			}
		);
	}
);
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