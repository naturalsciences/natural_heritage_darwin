<table class="catalogue_table_view taxon_view">
  <tr>
    <td>
    <?php if ($spec) : ?>
      <?php if ($spec->getTaxonName() != "") : ?>
        <?php echo link_to(__($spec->getTaxonName(ESC_RAW)), 'taxonomy/view?id='.$spec->getTaxonRef(), array('id' => $spec->getTaxonRef())) ?>
        <?php echo image_tag('info.png',"title=info class=info");?>
        <div class="tree">
        </div>
      <?php endif ; ?>
     <?php endif ; ?>
    </td>
  </tr>
</table>
<script type="text/javascript">
$(document).ready(function ()
{
   $('.taxon_view .info').click(function()
   {
     if($('.taxon_view .tree').is(":hidden"))
     {
		 //ftheeten 2017 30 11
		 <?php if (isset($spec)) : ?>
		  <?php if (is_object($spec)) : ?>
       $.get('<?php echo url_for('catalogue/tree?table=taxonomy&id='.$spec->getTaxonRef()) ; ?>',function (html){
         $('.taxon_view .tree').html(html).slideDown();
         });
		  <?php endif ; ?>
		  <?php endif ; ?>
     }
     $('.taxon_view .tree').slideUp();
   });
});
</script>

