<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php echo form_tag('gtu/gtuTranslation', array('class'=>'edition qtiped_form', 'id' => 'translation_form') );?>
<div class="page translate_modal">
<h1><?php echo __('Geographic translation service');?></h1>

<?php if($sf_params->get('with_js') == '1' || $sf_params->get('with_js') === true):?>

<input type="hidden" id="http_referer" name="http_referer" value="<?php print($_SERVER["HTTP_REFERER"]);?>">
<script language="javascript">
var translate_term=function(term)
{
		var url="<?php echo(url_for('gtu/JsonTranslation?'));?>";
			$.getJSON( url, {tag:term} ,function( data ) {
				$(".result_row").remove();
				var i=1;
				 $.each(data, function(index) {
						
						
						var source_table=data[index].source_table;
						var wikidata=data[index].wikidata;
						var name=data[index].reference_name;
						var translated_name=data[index].translated_name;
						var lang_iso=data[index].lang_iso;
						$('#table_translate tbody').append('<tr class="result_row" ><td>'+i+'</td><td><input type="checkbox" class="chk_translate" id="translate_row_'+i+'" value="'+translated_name+'"></td><td>'+lang_iso+'</td><td id="translate_val_'+i+'">'+translated_name+'</td><tr>');
					     i=i+1;
					});
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

		$("#trg_translate").click(
			function()
			{
				translate_term($("#text_to_translate").val());
			}
		);
		var tmp=$("#text_to_translate").val();
		if(tmp.length>0)
		{
			$("#trg_translate").click();
		}
		
		$(".choose_translate").click(
			function()
			{
				var translated=$.map($('.chk_translate:checked'), function(n, i){
					  return n.value;
				}).join(';');
				$(sessionStorage.getItem("translated_line")).val($(sessionStorage.getItem("translated_line")).val()+";"+translated);
				$('body').trigger('close_modal_gtu');
		    }
		);
	}

);



</script>
<?php endif;?>
To translate:
<input type="text" name="text_to_translate" id="text_to_translate" value="<?php print($tag);?>"/>
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
</form>
