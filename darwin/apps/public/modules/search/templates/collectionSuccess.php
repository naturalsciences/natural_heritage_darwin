
<?php slot('title', __('CollectionsStatistics'));  ?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<style>

table.results  {
   border: 1px solid black;
}

</style>
<div class="page">
  <h1><?php echo __('Collection statistics');?><?php (strlen($name)>0)? print(" (". __($name).")"):print("");?></h1>
 <table style="display:inline-block;">
 <tr>
     <td>
     <div style="display:none;"><?php print($form['id'])?></div><?php echo(__("All collections")); ?>
     </td>
     <td>
     <input type="checkbox" id="all_collections" name="all_collections" class="all_collections" <?php print($all_checked);?>/>
     </td>
 </tr>
 <tr>

     <td><?php echo __("Count subcollections");?></td>
     <td><input type="checkbox" name="count_subcollections" id="count_subcollections" <?php print($include_sub_checked); ?> /></td>
 </tr>
 <tr>
     <td><?php echo __("Display subcollections");?></td>
     <td><input type="checkbox" name="display_subcollections" id="display_subcollections" <?php print($display_sub_checked); ?>  /></td>
 </tr>
 <tr>
     <td><?php echo __("View data");?></td>
     <td><input type="checkbox" name="view_data" id="view_data"  <?php print($display_data_checked); ?>  /></td>
 </tr>
 </table>
 <div style="text-align:center;" ><input   type="submit" name="search" id="search" value="<?php echo __('Search'); ?>" /> 
  <br/></div>
<div id="div_loader" style="display:none;">
 <img src="<?php echo(public_path("images/loader.gif"));?>"></img>
 </div>
<div id="div_result" style="display:none;">
<br/>
<div class="div_specimens" style="display:none;">
	<h3>Specimens count :</h3>
	<br/>
	<div  class="results_container">
		<table name="results1" id="results1" class="results" >
		</table>
	</div>
</div>
<div class="div_types" style="display:none;">
	<h3>Types count :</h3>
	<div  class="results_container">
		<table name="results2" id="results2" class="results" >
		</table>
	</div>
</div>
<div class="div_taxa" style="display:none;">
<h3>Taxa in specimens count :</h3>
<div  class="results_container">
    <table name="results3" id="results3" class="results" >
    </table>
</div>
</div>
</div>
</div>
<script language="JavaScript">

var oldCollId="";
var finishedAjax1=false;
var finishedAjax2=false;
var finishedAjax3=false;

var goSpecimen=true;
var goType=true;
var goTaxa=true;



<?php if($selection):?>
goSpecimen=false;
goType=false;
goTaxa=false;
finishedAjax1=true;
finishedAjax2=true;
finishedAjax3=true;

<?php endif;?>
var reinitWaiter=function()
{
<?php $objs=array_map('strtolower', explode(",",$objects)); ?>
	<?php if(in_array("specimens", $objs)):?>
		goSpecimen=true;
		finishedAjax1=false;
	<?php endif;?>
	<?php if(in_array("types", $objs)):?>
		goType=true;
		finishedAjax2=false;
	<?php endif;?>
    <?php if(in_array("taxa", $objs)):?>
		goTaxa=true;
		finishedAjax3=false;
	<?php endif;?>

}


var hideLoader=function()
{
	$("#div_result").css("display", 'block');

    if(finishedAjax1 && finishedAjax2 && finishedAjax3)
    {
	
        $("#div_loader").css("display", 'none');
    }
}

// Builds the HTML Table out of myList.
var buildHtmlTable=function(myList, selector, view_data) 
{
    $(selector+" tr").remove();
      var columns = addAllColumnHeaders(myList, selector);

      for (var i = 0; i < myList.length; i++) {
        if(view_data||i == myList.length-1)
       { 
            var row = $('<tr/>');
            var goBold=false;	
            for (var colIndex = 0; colIndex < columns.length; colIndex++) 
            {
              
                  var cellValue = myList[i][columns[colIndex]];
                  if (cellValue == null) cellValue = "";
                  if(cellValue.toString().toLowerCase()=="total")
                  {            
                        
                        goBold=true;
                  }
                  if(goBold)
                  {
                    cellValue="<b>"+cellValue+"</b>";
                  }
                  row.append($('<td/>').html(cellValue));
               
            }
            $(selector).append(row);
        }
      }
}

// Adds a header row to the table and returns the set of columns.
// Need to do union of keys from all records as some records may not contain
// all records.
function addAllColumnHeaders(myList, selector) {
  var columnSet = [];
  var headerTr = $('<tr/>');

  for (var i = 0; i < myList.length; i++) {
    var rowHash = myList[i];
    for (var key in rowHash) {
      if ($.inArray(key, columnSet) == -1) {
        columnSet.push(key);
        key=key.replace(/_/g, " ");
        if(key.toLowerCase()=="total")
        {            
            key="<b>"+key+"</b>";
        }
        
        headerTr.append($('<th/>').html(key));
      }
    }
  }
  $(selector).append(headerTr);

  return columnSet;
}

var LastDayOfMonth=function(Year, Month) {
    var dateTmp= new Date( (new Date(Year, Month,1))-1 );
	return dateTmp.getDate();
}

var getStatistics = function(collection_id,  includesub, displaysub, includedata, selector)
	{
		
		
		var dataTmp={};
        view_data=includedata;
		if(collection_id.length>0)
		{
			dataTmp["collectionid"]=collection_id;
		}		
		
		if(includesub)
		{          
			dataTmp["withsubcollections"]="true";
		}
		if(displaysub)
		{
			dataTmp["withdetails"]="true";
		}
        
        if($(selector).attr('id')=="search")
        {
            $("#results1 tr").remove();
            $("#results2 tr").remove();
            $("#results3 tr").remove();
			if(goSpecimen)
			{
				var request1 = $.ajax({
				  url: "<?php echo url_for("search/display_statistics_specimens");?>",
				  method: "GET",
				  data: dataTmp,
				  dataType: "json"
				}).done(
					function(result)
					{
						finishedAjax1 = true;                    
						buildHtmlTable(result, "#results1", displaysub);
						hideLoader();
						$(".div_specimens").css("display", 'block');
						
					}
				);
            }
			
			if(goType)
			{
				var request2 = $.ajax({
				  url: "<?php echo url_for("search/display_statistics_types");?>",
				  method: "GET",
				  data: dataTmp,
				  dataType: "json"
				}).done(
					function(result)
					{
						finishedAjax2 = true;                   
						buildHtmlTable(result, "#results2", includedata);
						hideLoader();
						$(".div_types").css("display", 'block');
					}
				);
			}
			
			if(goTaxa)
			{
				var request3 = $.ajax({
				  url: "<?php echo url_for("search/display_statistics_taxa");?>",
				  method: "GET",
				  data: dataTmp,
				  dataType: "json"
				}).done(
					function(result)
					{
						finishedAjax3 = true;                   
						buildHtmlTable(result, "#results3", includedata);
						hideLoader();
						$(".div_taxa").css("display", 'block');
					}
				);
			}
        }
        else if($(selector).attr('id')=="search_csv")
        {
            $("#div_loader").css("display", 'none');
            var getQuery=$.param(dataTmp);            
            window.open("<?php echo url_for("search/display_all_statistics_csv");?>?"+getQuery);
            return false;
        }
	}
	
$(document).ready(	
	
    function()
    {
    
       $("#all_collections").change(
            function()
            {
                if(this.checked)
                {
                    oldCollId=$(".collection_ref").val();
                    $(".collection_ref").prop('disabled', true);
                    $(".collection_ref").val("/");
                }
                else
                {
                    $(".collection_ref").prop('disabled', false);
                    $(".collection_ref").val(oldCollId);
                }
            }
       
       );
       
       $("#search").on("click", 
           function(e)
           {
               e.preventDefault(); 
               reinitWaiter();

               $("#div_loader").css("display", 'block');

         
			
				getStatistics($(".collection_ref").val(), $("#count_subcollections").is(":checked"), $("#display_subcollections").is(":checked"), $("#view_data").is(":checked"), this )				
           }
       );
	   <?php if(array_key_exists("id",$_REQUEST)): ?>
        $("#search").click();
	   <?php endif; ?>
    }
);
</script>