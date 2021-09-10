<?php use_helper('Date');?>
<div class="page">
<h1><?php print($name);?></h1>

<br/>
<div class="wait" style="font-style: italic;" ></div>
<br/>
<div class='download_link' style="display:none"> <a class="bt_close" href="<?php echo url_for( 'savesearch/downloadTaxonomyFile?type_file='.$type_file.'&query_id='.$query_id);?>"><?php echo __("Download") ?></a></div>
</div>
<script>

var user_id = <?php print($user_id) ?>;
var query_id = <?php print($query_id) ?>;
var lock=false;
function get_page()
{
	console.log("call");
	$.getJSON("<?php print(url_for("savesearch/specimenReportGetCurrentPage")); ?>", {query_id : query_id, user_id:  user_id}, 
	function( data ) {
		
		
		lock=data.lock;
		
			
			if(lock)
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