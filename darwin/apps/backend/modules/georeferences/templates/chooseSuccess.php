<?php slot('title', __('Search Georeferences'));  ?>
<div class="page">
<h1><?php echo __('Georeferences search');?></h1>

<?php if($sf_params->get('with_js') == '1' || $sf_params->get('with_js') === true):?>

<input type="hidden" id="http_referer" name="http_referer" value="<?php print($_SERVER["HTTP_REFERER"]);?>">
<script language="javascript">
$(document).ready(function () {
    $('.result_choose').live('click',chooseGeoreferences);
   
});





function chooseGeoreferences(event)
{
	console.log("geo_choose");
	
	$('.result_choose').die('click');
    $('body').trigger('close_modal');
	
	
  el = $(this).closest('tr');
 
  ref_element_id = getIdInClasses(el);
  //ftheeten 2018 12 13
  cell=el.find('td.item_name');
  ref_element_name = cell.html();
  console.log(ref_element_id);
  console.log(ref_element_name);

  $("#gtu_georeference_ref").val(ref_element_id);
  $("#gtu_georeference_ref").trigger('change');
  $("#gtu_georeference_ref_name").html(ref_element_name);
  $('.result_choose').die('click');
  $('body').trigger('close_modal');
  
}






</script>
<?php endif;?>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => true)) ?>

</div>
