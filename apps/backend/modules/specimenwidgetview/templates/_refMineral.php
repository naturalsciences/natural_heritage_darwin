<table class="catalogue_table_view mineral_view">
  <tr>
    <td>
      <?php if ($spec->getMineralName() != "") : ?>
        <?php echo link_to(__($spec->getMineralName(ESC_RAW)), 'mineralogy/view?id='.$spec->getMineralRef(), array('id' => $spec->getMineralRef())) ?>
        <?php echo image_tag('info.png',"title=info class=info");?>
        <div class="tree">
        </div>
      <?php endif ; ?>
    </td>
  </tr>
</table>
<script type="text/javascript">
$(document).ready(function ()
{
   $('.mineral_view .info').click(function() 
   {
     if($('.mineral_view .tree').is(":hidden"))
     {
       $.get('<?php echo url_for('catalogue/tree?table=mineralogy&id='.$spec->getMineralRef()) ; ?>',function (html){
         $('.mineral_view .tree').html(html).slideDown();
         });
     }
     $('.mineral_view .tree').slideUp();
   });
});
</script>
