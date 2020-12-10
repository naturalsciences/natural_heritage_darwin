<div id="taxon_orig" class="hidden warn_message"><?php echo __('The taxon you chose, was marked as "renamed".');?>
<br />
<?php echo __('Click on the name below to replace the unit by its current name.');?>
<span></span>
</div>

<?php echo $form['taxon_ref']->renderError() ?>
<?php echo $form['taxon_ref']->render() ?>

<!--<B><label class="cites"></label></B></BR>-->
<table>
	<tr>
		<th>
			<?php echo "CITES" ?>:
		</th>
		<td>
			<table>
				<tr>
					<!--<td>
						 < ? php echo $form['cites']->renderError() ?>
						< ?php echo $form['cites'] ?>
					</td>-->
					<td class="cites" onclick="window.open($urltaxon,'name','width=1150,height=800')">
						Check this taxon :  <?php echo image_tag('info.png',"title=info class=info id=cites");?> ("Your search did not match any taxa" = not concerned by CITES)
					</td>
				</tr>
				<tr>
					<td onclick="window.open('https://www.cites.org/eng/app/appendices.php','name','width=1150,height=800')">
						List of species : <?php echo image_tag('info.png',"title=info class=info id=cites");?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<B><label class="taxonomy1"></label></B>
		<label class="taxonomy2"></label></BR></BR>
	</tr>
</table>

<!--ftheeten 2018 09 18-->
<input id="create_identification" type="button" value="Create identification"></input>

<script  type="text/javascript">
	function loadCurrent() {
		if($('#specimen_taxon_ref').val() != '') {
			// Fetch the current name of the taxa
			$.getJSON('<?php echo url_for('catalogue/getCurrent?table=taxonomy');?>/id/' + $('#specimen_taxon_ref').val(), function(data) {
				if(data.id) {
					$('#taxon_orig span').text(data.name).attr('r_id', data.id);
					$('#taxon_orig').removeClass('hidden');
				}
			});
		}
	}
	loadCurrent();
	$('#specimen_taxon_ref').bind('change',loadCurrent);
	$('#taxon_orig span').click(function() {
		$('#specimen_taxon_ref_name').val($('#taxon_orig span').text());
		$('#specimen_taxon_ref').val($('#taxon_orig span').attr('r_id'));
		$('#taxon_orig').addClass('hidden');
	});
  
     //ftheeten 2018 09 18--
	$("#create_identification").click(function(){
        if($("#specimen_taxon_ref_name").val().trim().length>0){
			$("#add_identification").click();
            document.getElementById('identification_placeholder').scrollIntoView();
            window.scrollBy(0, -110); 
        }else{
            alert("Please attribute a scientific name");
        }
    });

	$(document).ready(function (){
		var $val_id=$("#specimen_taxon_ref_name").val();

		if ($val_id != ""){
			var url=location.protocol + '//' + location.host + "/"+ location.pathname.split("/")[1] +  "/specimen/getCitesAndTaxonomy";
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
		
		//JMHerpers 12/9/2019
		$taxonarr = $("#specimen_taxon_ref_name").val().split(" ");
		testcites($("#specimen_taxon_ref_name").val(),$taxonarr);
			
		$(".cites").hover(function() {
		//	alert("change" + $(this).val());
			if($("#specimen_taxon_ref_name").val() != ""){
				$taxonarr = $("#specimen_taxon_ref_name").val().split(" ");
				testcites($("#specimen_taxon_ref_name").val(),$taxonarr);
			}
		 });
	});
	
	//JMHerpers 12/9/2019
	function testcites ($valtax,$taxonarray){
		if ($taxonarray.length > 2){
			$taxonval = $taxonarray[0] + "+" + $taxonarray[1];
		}else if (($taxonarray.length == 2) & ( $.inArray( $valtax.slice(-1), [ "1", "2","3","4","5","6","7","8", "9", "0", ")" ] ) == -1 )){
				$taxonval = $taxonarray[0] + "+" + $taxonarray[1];
		}else{
				$taxonval = $taxonarray[0];
		}
		if ($taxonarray.length == 1){
			$taxonval = $taxonarray[0];
		}
		
		$urltaxon = "http://checklist.cites.org/#/en/search/output_layout=alphabetical&level_of_listing=0&show_synonyms=1&show_author=1&show_english=1&show_spanish=1&show_french=1&scientific_name=" + $taxonval + "&page=1&per_page=20";
	}
</script>
