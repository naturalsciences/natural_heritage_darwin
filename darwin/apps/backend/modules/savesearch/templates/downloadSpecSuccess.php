<?php use_helper('Date');?>
<div class="page">
<h1><?php print($name);?></h1>
<table>
<tr>
<td>amount of records</td><td class="nb_records"><td>
</tr>
<tr>
<td>page size</td><td class="page_size"><td>
</tr>
<tr>
<td>current_page</td><td class="current_page"><td>
</tr>
</table>
<br/>
<div class="wait" style="font-style: italic;" ></div>
<br/>
<div class='download_link' style="display:none"> <a class="bt_close" href="<?php echo url_for( 'savesearch/downloadSpecimenFile?query_id='.$query_id);?>"><?php echo __("Download") ?></a></div>
</div>
<script>

var user_id = <?php print($user_id) ?>;
var query_id = <?php print($query_id) ?>;
var max_record=<?php print($total_size) ?>;
var size_records=0;
var current_page=0;
var lock=false;
function get_page()
{
	console.log("call");
	$.getJSON("<?php print(url_for("savesearch/specimenReportGetCurrentPage")); ?>", {query_id : query_id, user_id:  user_id}, 
	function( data ) {
		console.log("finished");
		$(".nb_records").html(data.nb_records);
		$(".page_size").html(data.page_size);
		$(".current_page").html(data.current_page); 
		lock=data.lock;
		
			current_page=data.current_page;
			if(current_page==1||size_records==0)
			{
				size_records=data.page_size;
			}
			console.log(parseInt(current_page)*parseInt(size_records));
			console.log(parseInt(max_record));
			if((parseInt(current_page)*parseInt(size_records))<parseInt(max_record)||parseInt(size_records)<parseInt(size_records)||size_records==0)
			{
				$(".wait").text("wait (query running). Do not close page");
				setTimeout(function () {
					 get_page();
				  }, 20000);
			}
			else
			{
				$(".wait").text("query finished, click to download button");
				$(".download_link").show();
			}
		
	}).done(function() {
    //console.log( "second success" );
  })
  .fail(function(xhr, err) { 

    console.log(xhr);
	 console.log(err);
	
});
}




$(document).ready(
	 function()
	{
          $(".wait").text("wait (query running). Do not close page");
        setTimeout(function(){
          console.log("load");
		get_page();
        }, 20000);
		
	}
);
</script>