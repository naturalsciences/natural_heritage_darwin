<div id="taxon_orig" class="hidden warn_message"><?php echo __('The taxon you chose, was marked as "renamed".');?>
<br />
<?php echo __('Click on the name below to replace the unit by its current name.');?>
<span></span>
</div>

<?php echo $form['taxon_ref']->renderError() ?>
<?php echo $form['taxon_ref']->render() ?>
<br/>
<?php print(__("Taxonomic classification :"));?>
<br/>
<?php echo $form['preferred_taxonomy']->renderError() ?>
<?php echo $form['preferred_taxonomy']->render() ?>

<B><label class="cites"></label></B></BR>
<B><label class="taxonomy1"></label></B>
<label class="taxonomy2"></label></BR></BR>

<!--ftheeten 2018 09 18-->
<input id="create_identification" type="button" value="Create identification"></input>

<script  type="text/javascript">
  function loadCurrent() {
    if($('#specimen_taxon_ref').val() != '') {
      // Fetch the current name of the taxa
      console.log( '<?php echo url_for('catalogue/getCurrent?table=taxonomy');?>/id/' + $('#specimen_taxon_ref').val());
      $.getJSON('<?php echo url_for('catalogue/getCurrent?table=taxonomy');?>/id/' + $('#specimen_taxon_ref').val(), function(data) {
        if(data.id) {
          $('#taxon_orig span').text(data.name).attr('r_id', data.id);
          $('#taxon_orig').removeClass('hidden');
        }
      });
    }
  }
  console.log("test syno");
  loadCurrent();
  $('#specimen_taxon_ref').bind('change',loadCurrent);
  $('#taxon_orig span').click(function(){
    $('#specimen_taxon_ref_name').val($('#taxon_orig span').text());
    $('#specimen_taxon_ref').val($('#taxon_orig span').attr('r_id'));
    $('#taxon_orig').addClass('hidden');
  }
  );
  
  //ftheeten 2018 09 18--
	$("#create_identification").click(function(){
            if($("#specimen_taxon_ref_name").val().trim().length>0)    {
                $("#add_identification").click();
           
                document.getElementById('identification_placeholder').scrollIntoView();
                window.scrollBy(0, -110); 
             }
             else{
                alert("Please attribute a scientific name");
             }
    });
	
	function GetCollectionInfo(id){
			var url=location.protocol + '//' + "<?php print(parse_url(sfContext::getInstance()->getRequest()->getUri(),PHP_URL_HOST ));?>" + "/backend.php/collection/descCollectionJSON";
			$.getJSON( 
				url,
				{id: id},
				function(data) {
					if($.isNumeric(data[0].preferred_taxonomy))
					{
						$(".col_check_metadata_ref option[value='"+data[0].preferred_taxonomy+"']").prop('selected', true)
					}
					else
					{
						$(".col_check_metadata_ref option:first").prop('selected', true)
					}
				}
			);
		};

	$(document).ready(function (){
		var $val_id=$("#specimen_taxon_ref_name").val();

		if ($val_id != ""){
			var url=location.protocol + '//' + "<?php print(parse_url(sfContext::getInstance()->getRequest()->getUri(),PHP_URL_HOST ));?>" +   "/backend.php/specimen/getCitesAndTaxonomy";
			$.get( 
				url,
				{id: $val_id},
				function(data) {
					var arraydata = data.split("--");
					if( arraydata[0] == 1){
						$(".cites").text("Taxon concerned by CITES !!!");
					}
					if( arraydata[1] != ""){
						$(".taxonomy1").text("Taxonomy: ");
						$(".taxonomy2").text(arraydata[1]);
					}
				}
			);
		}
		
		
		var align_collection_and_taxonomy=function(tax_id)
		{
				GetCollectionInfo(tax_id);
		}
		$("#specimen_collection_ref").change(function(){
			
			align_collection_and_taxonomy($("#specimen_collection_ref").val());
		});	
		
		if($("#specimen_collection_ref").val().length>0)
		{
			if($.isNumeric($("#specimen_collection_ref").val()))
			{
				align_collection_and_taxonomy($("#specimen_collection_ref").val());
			}
		}
	});
</script>
