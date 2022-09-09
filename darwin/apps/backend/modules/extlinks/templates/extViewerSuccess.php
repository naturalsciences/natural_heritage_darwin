
<div class="ui-tooltip-titlebar">
<div id="ui-tooltip-modal-title" class="ui-tooltip-title" aria-atomic="true" >View Hi-res image</div>
<a class="ui-state-default ui-tooltip-close ui-tooltip-icon" title="Close tooltip" aria-label="Close tooltip" role="button">
<span class="ui-icon ui-icon-close">x</span>
</a>
</div>


<span>
                    <iframe width="700" height="700" src="<?php echo($link->getUrl());?>" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" onmousewheel="" style="display: block;  margin: auto;"></iframe>
                    <br/>
                  
</span>
  <div  style="text-align:center;"><?php echo __("URL");?>: <a target="_blank" href="<?php echo(str_replace('/embed','', $link->getUrl()));?>" ><?php echo(str_replace('/embed','', $link->getUrl()));?></a></div>
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