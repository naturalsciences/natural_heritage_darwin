<div class="page">
  <h1><?php echo __('Choose an expedition');?></h1>
  <script language="javascript">
    $(document).ready(function () {
      $('.results tbody tr').live('click', function () {
          ref_element_id = getIdInClasses($(this));
          ref_element_name = $(this).children("td:first").text();
          $('.results tbody tr').die('click');
	  $('body').trigger('close_modal');
      });
    });
  </script>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => true)) ?>
  <br /><br />
</div>
