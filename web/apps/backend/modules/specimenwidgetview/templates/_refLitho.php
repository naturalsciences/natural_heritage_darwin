<table class="catalogue_table_view litho_view">
  <tr>
    <td>
      <?php if ($spec->getLithoRef() != "") : ?>
        <?php echo link_to(__($spec->getLithoName(ESC_RAW)), 'lithostratigraphy/view?id='.$spec->getLithoRef(), array('id' => $spec->getLithoRef())) ?>
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
   $('.litho_view .info').click(function() 
   {
     if($('.litho_view .tree').is(":hidden"))
     {
       $.get('<?php echo url_for('catalogue/tree?table=lithostratigraphy&id='.$spec->getLithoRef()) ; ?>',function (html){
         $('.litho_view .tree').html(html).slideDown();
         });
     }
     $('.litho_view .tree').slideUp();
   });
});
</script>
