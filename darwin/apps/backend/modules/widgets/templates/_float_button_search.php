<p id="float_button">
<?php if(strpos($_SERVER["REQUEST_URI"],"/specimensearch/search")&&!strpos($_SERVER["REQUEST_URI"],"/criteria/")):?>
        <?php echo image_tag('previous.png', array("id"=>"back_float" ));?>
<?php elseif(strpos($_SERVER["REQUEST_URI"],"/specimensearch")): ?>    
    <?php echo image_tag('button_ok.png', array("id"=>"search_float" ));?>
<?php endif;?>
<?php echo image_tag('save.png', array("id"=>"save_float" ));?>
</p>
<script language="javascript">
$(document).ready(
    function()
    {
       $("#search_float").click(
            function()
            {                
                $("#submit").click();
            }
       );
       
       $("#save_float").click(
            function()
            {
                $("#save_search").click();
            }
       );
       
       $("#back_float").click(
            function()
            {
                $("#criteria_butt").click();
            }
       );
       
     }
   );

</script>
