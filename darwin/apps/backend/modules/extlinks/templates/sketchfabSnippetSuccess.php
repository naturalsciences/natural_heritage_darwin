<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<div class="ui-tooltip-titlebar">
<div id="ui-tooltip-modal-title" class="ui-tooltip-title" aria-atomic="true" >View 3D Snippet</div>
<a class="ui-state-default ui-tooltip-close ui-tooltip-icon" title="Close tooltip" aria-label="Close tooltip" role="button">
<span class="ui-icon ui-icon-close">×</span>
</a>
</div>

<?php
	if(strpos($link->getUrl(), "//sketchfab.com"))
	{
		$display_url=$link->getUrl()."/embed";
		
	}
	else
	{
		$display_url=$link->getUrl();
	}
	$link_url=$link->getUrl();
?>
<span>
                    <iframe width="640" height="480" src="<?php echo($display_url);?>" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" onmousewheel="" style="display: block;  margin: auto;"></iframe>
                    <br/>
                  
</span>
  <div  style="text-align:center;"><?php echo __("URL");?>: <a target="_blank" href="<?php echo($link_url);?>" ><?php echo($link_url);?></a></div>
<script language="javascript">

$(document).ready(function () {
//var last_position = $('html').offset().top;
//alert(last_position);
$(".ui-tooltip-close").click(
    function()
    {
        
            $('body').trigger('close_modal');
    }
);
});
</script>

