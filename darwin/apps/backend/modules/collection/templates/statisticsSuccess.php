
<?php slot('title', __('CollectionsStatistics'));  ?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<div class="page">
  <h1><?php echo __('Collection statistics');?></h1>
  <div style="text-align:right;">
    <input type="button" href="<?php echo url_for("collection/display_all_statistics_csv");?>" target="_blank" class="search_submit edition" name="search_csv" id="search_csv" value="<?php echo __('Get tab file'); ?>"/>
  </div>
 <table>
 <tr>
 <td>
 <?php echo(__("Collection")); ?> 
 </td>
 <td>
 <?php echo(__("All collections")); ?>  <input type="checkbox" id="all_collections" name="all_collections" class="all_collections"/>
	<div class="treelist collection_tree_div" style="border:solid;">
		    <?php echo $form['id'] ; ?>
            <br/>
      </div>
       <br/>
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
<div style="text-align:center">
<input class="search_submit" style="float: none;" type="submit" name="search" id="search" value="<?php echo __('Search'); ?>" /> 
</div>
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
<br/>

</div>
<script language="JavaScript">

var oldCollId="";
var finishedAjax1=false;
var finishedAjax2=false;
var finishedAjax3=false;

var hideLoader=function()
{
    if(finishedAjax1&&finishedAjax2&&finishedAjax3)
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

var getStatistics = function(collection_ids, ig_num, from_date, to_date, includesub, displaysub, selector)
	{
		
		console.log(collection_ids)
		var dataTmp={};
		if(collection_ids.length>0)
		{
			dataTmp["collectionids"]=collection_ids.join();
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
            var request1 = $.ajax({
              url: detect_https("<?php echo url_for("collection/display_statistics_specimens");?>"),
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
              url: detect_https("<?php echo url_for("collection/display_statistics_types");?>"),
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
              url: detect_https("<?php echo url_for("collection/display_statistics_taxa");?>"),
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
        }
        else if($(selector).attr('id')=="search_csv")
        {
            $("#div_loader").css("display", 'none');
            var getQuery=$.param(dataTmp);            
            window.open(detect_https("<?php echo url_for("collection/display_all_statistics_csv");?>?"+getQuery));
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
                    $('.col_check').prop('checked', true);
                }
                else
                {
                    $('.col_check').prop('checked', false);
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
				
			    var selected_collections = [];
				 $('.col_check:checked').each(function() {
					
				   selected_collections.push($(this).val());
				 });
				getStatistics(selected_collections, $(".ig_num").val(), date_from, date_to,$("#count_subcollections").is(":checked"), $("#display_subcollections").is(":checked"), this )				
           }
       );
	   
	   //collection treelist
	   
	       //ftheeten 2018 10 04
    var original_tree = $('.collection_tree_div').html();
    
     $(".do_reinit_collection").click(
        function()        
        {

             $('.collection_tree_div').html(original_tree);
             $('.treelist li:not(li:has(ul)) img.tree_cmd').hide();
             
            
        }
    );
    
    var filter_collection_logic=function()
		{
            //$('.container').html(original_tree);
			var searched_value=$(".filter_collection").first().val();			

            $(".treelist").each(function(iTree, tree)
                {
                    spans=$(tree).find("span");
                            spans.each(function(i, elem )
                            {
                                
                                var coll_name=$(elem).text();
                               
                                if (coll_name.toLowerCase().indexOf(searched_value.toLowerCase())!=-1) 
                                {		
                             
                                    $(elem).parents("li").show();
                                    $(elem).parents("li").css("visibility", "visible"); 
                                    $(elem).parents("li").parents("ul").show();
                                    $(elem).parents("li").addClass("collection_expanded");
                                    $(elem).parents("li").parents("ul").addClass("collection_expanded");
                                    
                                    
                                    
                                }
                                else
                                {
                                 
                                   
                                    if(! $(elem).parent("div").hasClass("collection_expanded"))
                                    {
                                       
                                        $(elem).parent("div").parent("li").css("display", "none");
                                    }
                                }
                            });
                });
			
		}
    
    //ftheeten 2018 10 03
	$(".do_filter_collection").click(
        function()
        {
                    
            filter_collection_logic();
        }
	);
    
        onElementInserted('body', '.collapsed', function(element)
        {
           $('.collapsed').click(function()
            {
                $(this).hide();
                $(this).siblings('.expanded').show();
                $(this).parent().siblings('ul').show();
            });
            
        });
        
   onElementInserted('body', '.expanded', function(element)
        {
          $('.expanded').click(function()
            {
                $(this).hide();
                $(this).siblings('.collapsed').show();
                $(this).parent().siblings('ul').hide();
            });
            
        });
    //end collections search engine

    $('.treelist li:not(li:has(ul)) img.tree_cmd').hide();
    $('.chk input').change(function()
    {
      li = $(this).closest('li');
      if(! $(this).is(':checked'))
        li.find(':checkbox').not($(this)).removeAttr('checked').change();
      else
        li.find(':checkbox').not($(this)).attr('checked','checked').change();
    });

    $('#clear_collections').click(function()
    {
       $('table.widget_sub_table').find(':checked').removeAttr('checked').change();
    });

    $('.collapsed').click(function()
    {
        $(this).addClass('hidden');
        $(this).siblings('.expanded').removeClass('hidden');
        $(this).parent().siblings('ul').show();
    });

    $('.expanded').click(function()
    {
        $(this).addClass('hidden');
        $(this).siblings('.collapsed').removeClass('hidden');
        $(this).parent().siblings('ul').hide();
    });

    $('#check_editable').click(function(){
      $('.treelist input:checked').removeAttr('checked').change();
      $('li[data-enc] > div > label > input:checkbox').attr('checked','checked').change();
    });
    
      <?php if($id>0):?>
            //init on load
            $('.col_check[value="<?php print($id);?>"]').attr('checked', true);
            $("#search").click();

      <?php endif;?> 
    }
);
</script>