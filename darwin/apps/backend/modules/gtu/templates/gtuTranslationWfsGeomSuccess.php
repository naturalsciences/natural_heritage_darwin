<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php echo form_tag('gtu/gtuTranslationWfsGeom', array('class'=>'edition qtiped_form', 'id' => 'translation_form') );?>
<div class="page translate_modal">
<h1><?php echo __('Geographic translation service');?></h1>


To translate:
<input type="text" name="wfs_layer_translate_geom" id="wfs_layer_translate_geom" value="<?php print($layer);?>"/>
<input type="text" name="wfs_ids_geom" id="wfs_ids_geom" value="<?php print($ids);?>"/>
Select All :
<input type="checkbox" name="select_all" id="select_all"/>
<input type="button" name="trg_translate" id="trg_translate" value="Translate"></input>
<input type="button" name="choose_translate1" id="choose_translate1" class="choose_translate" value="Choose"></input>

<div class="results_container">
<table id="table_translate" class="results">
 <thead>
<tr>
<th></th>
<th>Choose</th>
<th>Language</th>
<th>Translated name</th>
<th>Reference name</th>
</tr>
</thead>
<tbody>
</tbody>
</table>

</div>

<script>


</script>
<input type="button" name="choose_translate2" id="choose_translate2" class="choose_translate" value="Choose"></input>
</div>
<div class="loading_div"></div>
<?php if($sf_params->get('with_js') == '1' || $sf_params->get('with_js') === true):?>

<input type="hidden" id="http_referer" name="http_referer" value="<?php print($_SERVER["HTTP_REFERER"]);?>">
<script language="javascript">
var loading=false;
var translate_wfs_term=function(layer, ids)
{
		$(".loading_div").html("Loading, please wait...");
		var url="<?php echo(url_for('gtu/JsonTranslationWfsGeom?'));?>";
			$.getJSON( url, {layer:layer, ids:ids} ,function( data ) {
				$(".result_row").remove();
				var i=1;
				 $.each(data, function(index) {
						
						loading=false;
						var source_table=data[index].source_table;
						var wikidata=data[index].wikidata;
						var name=data[index].full_name;
						var translated_name=data[index].translated_name;
						var lang_iso=data[index].lang_iso;
						$('#table_translate tbody').append('<tr class="result_row" ><td>'+i+'</td><td><input type="checkbox" class="chk_translate_geom" id="translate_row_'+i+'" value="'+translated_name+'"></td><td style="max-width:300px;word-wrap: break-word;">'+lang_iso+'</td><td id="translate_val_'+i+'"><b>'+translated_name+'</b></td><td id="translate_ref_val_'+i+'">'+name+'</td><tr>');
					     i=i+1;
					});
					$(".loading_div").html("");
		});
}



$(document).keypress(function(e) {
  if(e.which == 13) {
	  e.preventDefault();
    $("#trg_translate").click();
  }
});


$(document).ready(
	function()
	{
		 console.log("LOAD");

		$("#trg_translate").click(
			function()
			{
				if(!loading)
				{
					loading=true;
					translate_wfs_term($("#wfs_layer_translate_geom").val(), $("#wfs_ids_geom").val());
				}
			}
		);
		

		$(".choose_translate").click(
			function()
			{
				var translated=$.map($('.chk_translate_geom:checked'), function(n, i){
					  return n.value;
				}).join('|');
				$(sessionStorage.getItem("translated_line_wfs")).val(translated);
				$('body').trigger('close_modal_gtu');
		    }
		);
		
		$('#select_all').change(function() {
		   if ($(this).is(':checked')) {
			   $('.chk_translate_geom').prop('checked', true);
		   } else {
			  $('.chk_translate_geom').prop('checked', false);
		   }
		});
		setTimeout(function()
		{
			$("#trg_translate").click();
			
		},1000);
		
	}

);



</script>
<?php endif;?>
</form>
