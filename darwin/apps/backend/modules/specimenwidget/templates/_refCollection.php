<?php slot('widget_mandatory_refCollection',true);  ?>
<?php echo $form['collection_ref']->renderError() ?>


<?php if(isset($view) && $view) : ?>
  <?php echo $form->getObject()->Collections->getName() ; ?>
<?php else  : ?>
  <?php echo $form['collection_ref']->render() ?>
<?php endif; ?>

<script>
widgcol_loaded=true;

var init_page=function()
{
	test_col();
	widgcol_loaded=true;
}
var test_col=function()
{
	
	var tmp=$("#specimen_collection_ref").val();
	if($.isNumeric(tmp))
	{	  
	    url_autocomplete_taxon_init_from_coll_current_coll=tmp;
		if(!widgcol_loaded)
		{
			$("#coll_scope_coll_only").prop("checked", true);
		}
		
		var tmp_mode=$('input[name="coll_scope"]:checked').val();
		console.log(tmp_mode);
		mode_autocomplete_taxa=tmp_mode;
		if(mode_autocomplete_taxa=="coll_only")
		{
			url_autocomplete_taxon="<?php print(url_for("catalogue/completeNameTaxonomyWithRef")); ?>?coll="+url_autocomplete_taxon_init_from_coll_current_coll;
			url_autocomplete_taxon_init_from_coll=true;
		}
		else
		{
			url_autocomplete_taxon="<?php print(url_for("catalogue/completeNameTaxonomyWithRef")); ?>";
		}
		console.log("_A");
		console.log(url_autocomplete_taxon);
		
		$("#specimen_taxon_ref_name").catcomplete(
			{
				"source":url_autocomplete_taxon
			}
		 );
		
		
		
	  
	}
}
$(document).ready(
	function()
	{
		$("#specimen_collection_ref").change(
			function()
			{
				test_col();
			}
		);
		
		init_page();
	});
		
	

</script>



