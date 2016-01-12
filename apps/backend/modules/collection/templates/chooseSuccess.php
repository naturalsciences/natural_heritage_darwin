<div class="page">
    <h1><?php echo __('Choose a collection');?></h1>
<script language="javascript">
$(document).ready(function () {
    $('.col_name span').click(function () {
      ref_element_id = getIdInClasses($(this).parent().parent());
      ref_element_name = $(this).text();
      $('body').trigger('close_modal');
    });
});
</script>
    <?php include_partial('collectionTree', array('institutions' => $institutions,'is_choose' => true)) ?>
    <br /><br />
</div>
