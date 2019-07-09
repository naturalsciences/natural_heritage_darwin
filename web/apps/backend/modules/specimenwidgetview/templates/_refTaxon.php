<table class="catalogue_table_view taxon_view">
  <tr>
    <td>
      <?php if ($spec->getTaxonName() != "") : ?>
        <?php echo link_to(__($spec->getTaxonName(ESC_RAW)), 'taxonomy/view?id='.$spec->getTaxonRef(), array('id' => $spec->getTaxonRef())) ?>
        <?php echo image_tag('info.png',"title=info class=info");?>
		</BR></BR>
		<B><label class="cites"></label></B></BR>
		<B><label class="taxonomy1"></label></B>
		<label class="taxonomy2"></label>
        <div class="tree">
        </div>
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
       $.get('<?php echo url_for('catalogue/tree?table=taxonomy&id='.$spec->getTaxonRef()) ; ?>',function (html){
         $('.taxon_view .tree').html(html).slideDown();
         });
     }
     $('.taxon_view .tree').slideUp();
   });
   
   //JMHerpers 2019 05 02
	//  console.log("test ");
	<?php if ($spec->getTaxonomy()->getCites()) : ?>
		$(".cites").text("!!!!  Taxon concerned by CITES !!!!");		
	<?php endif ; ?>
	<?php if ($spec->getTaxonomy()->getMetadataRef() != "") : ?>	
		$(".taxonomy1").text("     Taxonomy:");
		$(".taxonomy2").text(" <?php print($metad->getTaxonomyName())?>");
	<?php endif ; ?>

});
</script>

