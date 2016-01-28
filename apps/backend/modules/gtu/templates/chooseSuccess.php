<?php slot('title', __('Search sampling location'));  ?>
<div class="page">
<h1><?php echo __('Sampling location search');?></h1>

<?php if($sf_params->get('with_js') == '1' || $sf_params->get('with_js') === true):?>

<script language="javascript">
$(document).ready(function () {
    $('.result_choose').live('click',chooseGtu);
});

function chooseGtu()
{
  el = $(this).closest('tr');
  ref_element_id = getIdInClasses(el);
  ref_element_name = el.find('td.item_name').html();
  $('.result_choose').die('click');
  $('body').trigger('close_modal');
}

function chooseGtuInMap(id)
{
  ref_element_id = id;
  ref_element_name = $('.map_result_id_'+id+' .item_name').html();
  $('body').trigger('close_modal');
}

</script>
<?php endif;?>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => true)) ?>

</div>
