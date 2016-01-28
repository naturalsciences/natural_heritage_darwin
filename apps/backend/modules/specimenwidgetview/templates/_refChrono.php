<table class="catalogue_table_view chrono_view">
  <tr>
    <td>
      <?php if ($spec->getChronoRef() != "") : ?>
        <?php echo link_to(__($spec->getChronoName(ESC_RAW)), 'chronostratigraphy/view?id='.$spec->getChronoRef(), array('id' => $spec->getChronoRef())) ?>
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
   $('.chrono_view .info').click(function() 
   {
     if($('.chrono_view .tree').is(":hidden"))
     {
       $.get('<?php echo url_for('catalogue/tree?table=chronostratigraphy&id='.$spec->getChronoRef()) ; ?>',function (html){
         $('.chrono_view .tree').html(html).slideDown();
         });
     }
     $('.chrono_view .tree').slideUp();
   });
});
</script>
