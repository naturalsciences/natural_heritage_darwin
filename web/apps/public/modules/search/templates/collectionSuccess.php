
<?php slot('title', __('CollectionsStatistics'));  ?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<style>

table.results  {
   border: 1px solid black;
}

</style>
<div class="page">
  <h1><?php echo __('Collection statistics');?></h1>
 <table>
 <tr>
 <td>
 <?php echo(__("Collection")); ?> 
 </td>
 <td>
 <?php echo($form["id"]); ?> 
 </td>
 <td>
 <?php echo(__("All collections")); ?>  <input type="checkbox" id="all_collections" name="all_collections" class="all_collections"/>
 </td>
 </tr>
 <tr>
 <td>
  <?php echo($form["ig_num"]->renderLabel()); ?>
 </td>
 <td>
 <?php echo($form["ig_num"]); ?>
 </td>
 </tr>
 <tr>
 <td> 
  <?php echo($form["from_date"]->renderLabel()); ?>
 </td>
 <td>
 <?php echo($form["from_date"]); ?>
 </td>
 <td> 
  <?php echo($form["to_date"]->renderLabel()); ?>
 </td>
 <td>
  <?php echo($form["to_date"]); ?>
 </td>
 </tr>
 <tr>
 <td><?php echo __("Count subcollections");?></td>
 <td><input type="checkbox" name="count_subcollections" id="count_subcollections" checked/></td>
 <tr>
 <td><?php echo __("Display subcollections");?></td>
 <td><input type="checkbox" name="display_subcollections" id="display_subcollections" checked/></td>
 </tr>
 </table>
 <div style="text-align: center;"><input class="search_submit" type="submit" name="search" id="search" value="<?php echo __('Search'); ?>" /> 
  <a href="<?php echo url_for("search/display_all_statistics_csv");?>" target="_blank" class="search_submit" name="search_csv" id="search_csv"><?php echo __('Get tab file'); ?></a></div>
<div id="div_loader" style="display:none;">
 <img src="<?php echo(public_path("images/loader.gif"));?>"></img>
 </div>
<br/>
Specimens count :
<br/>
<div  class="results_container">
    <table name="results1" id="results1" class="results" >
    </table>
</div>
<br/>
Types count :
<div  class="results_container">
    <table name="results2" id="results2" class="results" >
    </table>
</div>
<br/>
Taxa in specimens count :
<div  class="results_container">
    <table name="results3" id="results3" class="results" >
    </table>
</div>

Higher taxa:
<div  class="results_container">
    <table name="results4" id="results4" class="results" >
    </table>
</div>
<br/>

</div>
<script language="JavaScript">

var oldCollId="";
var finishedAjax1=false;
var finishedAjax2=false;
var finishedAjax3=false;
var finishedAjax4=false;


var hideLoader=function()
{
    if(finishedAjax1&&finishedAjax2&&finishedAjax3&&finishedAjax4)
    {
        $("#div_loader").css("display", 'none');
    }
}

// Builds the HTML Table out of myList.
var buildHtmlTable=function(myList, selector) {
    $(selector+" tr").remove();
  var columns = addAllColumnHeaders(myList, selector);

  for (var i = 0; i < myList.length; i++) {
    var row = $('<tr/>');
    var goBold=false;	
    for (var colIndex = 0; colIndex < columns.length; colIndex++) {
	  
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

var getStatistics = function(collection_id, ig_num, from_date, to_date, includesub, displaysub, selector)
	{
		
		
		var dataTmp={};
		if(collection_id.length>0)
		{
			dataTmp["collectionid"]=collection_id;
		}
		if(ig_num.length>0)
		{
			dataTmp["ig_num"]=ig_num;
		}
		if(from_date.length>0)
		{
			dataTmp["creation_date_min"]=from_date;
		}
		if(to_date.length>0)
		{
			dataTmp["creation_date_max"]=to_date;
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
            $("#results4 tr").remove();
            var request1 = $.ajax({
              url: "<?php echo url_for("search/display_statistics_specimens");?>",
              method: "GET",
              data: dataTmp,
              dataType: "json"
            }).done(
                function(result)
                {
                    finishedAjax1 = true;                    
                    buildHtmlTable(result, "#results1");
                    hideLoader();
                    
                }
            );
           
            var request2 = $.ajax({
              url: "<?php echo url_for("search/display_statistics_types");?>",
              method: "GET",
              data: dataTmp,
              dataType: "json"
            }).done(
                function(result)
                {
                    finishedAjax2 = true;                   
                    buildHtmlTable(result, "#results2");
                    hideLoader();
                }
            );
            
            var request3 = $.ajax({
              url: "<?php echo url_for("search/display_statistics_taxa");?>",
              method: "GET",
              data: dataTmp,
              dataType: "json"
            }).done(
                function(result)
                {
                    finishedAjax3 = true;                   
                    buildHtmlTable(result, "#results3");
                    hideLoader();
                }
            );
            
             var request4 = $.ajax({
              url: "<?php echo url_for("search/display_higher_taxa");?>",
              method: "GET",
              data: dataTmp,
              dataType: "json"
            }).done(
                function(result)
                {
                    finishedAjax4 = true;                   
                    buildHtmlTable(result, "#results4");
                    hideLoader();
                }
            );
            
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
       
       $(".search_submit").on("click", 
           function(e)
           {
                  e.preventDefault(); 
                finishedAjax1=false;
                finishedAjax2=false;
                finishedAjax3=false;
                finishedAjax4=false;

               $("#div_loader").css("display", 'block');
               var date_from="";
			   if($("#statistics_from_date_year").val().length>0)
			   {
				   date_from=$("#statistics_from_date_year").val();
				    if($("#statistics_from_date_month").val().length>0)
					{
						date_from=date_from+"-"+$("#statistics_from_date_month").val();
					}
					else
					{
						date_from=date_from+"-01";
					}
					if($("#statistics_from_date_day").val().length>0)
					{
						date_from=date_from+"-"+$("#statistics_from_date_day").val();
					}
					else
					{
						date_from=date_from+"-01";
					}
				   
			   }
			  
				
				var date_to="";
			   if($("#statistics_to_date_year").val().length>0)
			   {
				   date_to=$("#statistics_to_date_year").val();
				    var month="01";
				    if($("#statistics_to_date_month").val().length>0)
					{
						month=$("#statistics_to_date_month").val();
						
					}
					date_to=date_to+"-"+month;
					
					if($("#statistics_to_date_day").val().length>0)
					{
						date_to=date_to+"-"+$("#statistics_to_date_day").val();
					}
					else
					{
						date_to=date_to+"-"+LastDayOfMonth($("#statistics_to_date_year").val(), month);
					}
				   
			   }
				
			
				getStatistics($(".collection_ref").val(), $(".ig_num").val(), date_from, date_to,$("#count_subcollections").is(":checked"), $("#display_subcollections").is(":checked"), this )				
           }
       );
       
       $("#search").click();
    }
);
</script>