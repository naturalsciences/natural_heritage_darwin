<?php slot('title', __('Search People'));  ?>        
<div class="page">
<h1><?php echo __('Search People');?></h1>

  <?php if($sf_params->get('with_js') === true):?>

<script  type="text/javascript">
  $(document).ready(function () {
    $('.result_choose').live('click',function () {
      el = $(this).closest('tr');
      ref_element_id = getIdInClasses(el);
      ref_element_name = el.find('td.item_name').text();
      if(typeof fct_update=="function")
      {
        fct_update(ref_element_id, ref_element_name);
      }
      else
      {
        $('.result_choose').die('click');
	      $('body').trigger('close_modal');
      }
    });
  });
  </script>
  <?php endif;?>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => true)) ?>
</div>
