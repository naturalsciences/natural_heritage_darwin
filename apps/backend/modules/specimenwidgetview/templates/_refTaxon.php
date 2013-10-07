<table class="catalogue_table_view">
  <tr>
    <td><?php echo $spec->getTaxonName() ; ?>
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
});
</script>
