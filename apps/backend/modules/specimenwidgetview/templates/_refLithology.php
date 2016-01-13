<table class="catalogue_table_view lithology_view">
  <tr>
    <td>
      <?php if ($spec->getLithologyRef() != "") : ?>
        <?php echo link_to(__($spec->getLithologyName(ESC_RAW)), 'lithology/view?id='.$spec->getLithologyRef(), array('id' => $spec->getLithologyRef())) ?>
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
   $('.lithology_view .info').click(function() 
   {
     if($('.lithology_view .tree').is(":hidden"))
     {
       $.get('<?php echo url_for('catalogue/tree?table=lithology&id='.$spec->getLithologyRef()) ; ?>',function (html){
         $('.lithology_view .tree').html(html).slideDown();
         });
     }
     $('.lithology_view .tree').slideUp();
   });
});
</script>
