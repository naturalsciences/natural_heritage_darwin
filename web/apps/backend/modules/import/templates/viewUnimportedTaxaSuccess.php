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

<table>
<tr>
<th>Message</th>
<th>Count</th>
</tr>
 <?php $sum=0; $i=0; foreach($stats as $stat):?>
    <tr>
		<?php $text; $text = $stat['import_exception'] ?: "None" ?>
         <td><a  href="javascript:filter_stats('<?php print($text);?>')" ><?php print($text);?></a></td>
         <td><?php $sum+=(int)$stat['count'];  print($stat['count']);?></td>           
    </tr>
 <?php endforeach;?>
 <tr><td><a href="javascript:reinit_stats();" >Total : </a></td><td><?php print($sum);?></td>
</table>

<br/>
<a href="<?php print(url_for("import/downloadTaxonomicStaging"));?>?import_ref=<?php print($id);?>"><?php print(__("Download all"));?></a>
&nbsp;
<a href="<?php print(url_for("import/downloadTaxonomicStaging"));?>?import_ref=<?php print($id);?>&unimported=on"><?php print(__("Download unimported"));?></a>
&nbsp;
<a href="<?php print(url_for("import/rechecktaxonomy"));?>?id=<?php print($id);?>"><?php print(__("Recheck and reimport"));?></a>
<br/>
<br/>
<table id="result_taxa" name="result_taxa" >
 <tr> 
        <th>id</th>
        <th>name</th>
        <th>level_ref</th>
        <th>name_cluster</th>
        <th>imported</th>
        <th>import_exception</th>
        <th>compare hierarchies</th>
        <th>Import</th>
 </tr>
 <?php $i=0; foreach($items as $item):?>
    <tr>
		<?php $text; $text = $item['import_exception'] ?: "None" ?>
         <td><?php print($item['id']);?></td>
         <td><?php print($item['name']);?></td>
         <td><?php 
                    $level=Doctrine::getTable("CatalogueLevels")->find($item['level_ref']);
                    print($level->getLevelName());?></td> 
         <td><?php print($item['name_cluster']);?></td>
         <td><?php print($item['imported'] ? "TRUE" : "FALSE");?></td>
         <td><?php print($text);?></td>
         <td><table><tr><td><b>Staging hierarchy</b><br/><?php print($item['staging_hierarchy']);?></td></tr>
                    <tr><td><b>Darwin hierarchy</b><br/><?php print($item['darwin_hierarchy']);?></td></tr>
         </table></td>
          <td>
                <?php if(!$item['imported']):?>
                    <a target="_blank" href="<?php print(url_for("taxonomy/new"));?>?taxonomy[name]=<?php print($item['name']);?>&taxonomy[level_ref]=<?php print($item['level_ref']);?>&taxonomy[metadata_ref]=<?php print($metadata_ref);?>"><?php print(__("Create taxon"));?></a>
                <?php endif;?>
          </td>
                      
    </tr>
 <?php endforeach;?>   
</table>

<br>

<script>
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