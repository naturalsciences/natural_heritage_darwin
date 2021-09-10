<?php use_helper('Date');?>
<div class="page">
<h1><?php print($name);?></h1>
<div class="wait" style="font-style: italic;white-space: pre-wrap;" ></div>
<br/>
<div class='download_link' style="display:none"> <a class="bt_close" href="<?php echo url_for( 'savesearch/downloadVirtualCollectionsFile?query_id='.$query_id.'&user_ref='.$user_ref);?>"><?php echo __("Download") ?></a></div>
</div>
<script>

var query_id = <?php print($query_id) ?>;
 
var start= new Date();
var date_string="";

function get_page()
{
	
	$.getJSON("<?php print(url_for("savesearch/testVirtualCollectionsReportRunning")); ?>", {query_id : query_id}, 
	function( data ) {
		
		    var start2= new Date();
			 date_string=date_string+"\n Last check : "+start2.getHours() + ":" + start2.getMinutes() + ":" + start2.getSeconds();
		    
			state=data.state;
			
			
			if(state=="running")
			{
				$(".wait").text(date_string);
				setTimeout(function () {
					 get_page();
				  }, 20000);
			}
			else if(state=="issue")
			{
				$(".wait").text("Report not generated problem occured");
				$(".download_link").show();
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
		

         date_string="wait (query running). Do not close page.\n Started at  : "+start.getHours() + ":" + start.getMinutes() + ":" + start.getSeconds();
          $(".wait").text(date_string);
        setTimeout(function(){
          console.log("load");
		get_page();
        }, 20000);
		
	}
);
</script>