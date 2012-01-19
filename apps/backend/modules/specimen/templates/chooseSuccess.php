<div class="page host_search">
  <h1><?php echo __('Choose a specimen');?></h1>
  <script language="javascript">
    $(document).ready(function () {
      $('.results tbody tr').live('click', function () {
          ref_element_id = getIdInClasses($(this));
          ref_element_name = '';
          $(this).children("td:nth-child(2)").find("ul li").each(function(index) {ref_element_name = ref_element_name + '[' + $(this).text().trim() + '] ';});
          $('.results tbody tr').die('click');
	  $('body').trigger('close_modal');
      });
    });
  </script>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => true)) ?>
  <br /><br />
</div>
