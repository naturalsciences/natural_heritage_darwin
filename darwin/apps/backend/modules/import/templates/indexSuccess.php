<?php slot('title', __('Import files summary'));  ?>

<div class="page">

<h1><?php echo __('Imports');?> : <?php echo image_tag('info.png',array('title'=>'info','class'=>'extd_info')); ?></h1>
    <?php include_partial('searchForm', array('form' => $form,'format' => $format)) ?>
</div>

<script language="javascript">
$(document).ready(function () {
  $('#import_filter').submit();
  $('#imports_filters_state').change(function()
  {
    if(/^to_be_loaded|loading|loaded|checking|pending|processing$/.test($(this).val()))
      $('#imports_filters_show_finished').removeAttr('checked');
    else
      $('#imports_filters_show_finished').attr('checked','checked');
  });
  $(".extd_info").each(function ()
  {
    $(this).qtip({
      show: { solo: true, event:'click' },
      hide: { event:false },
      style: 'ui-tooltip-light ui-tooltip-rounded ui-tooltip-dialogue',
      content: {
        text: '<img src="/images/loader.gif" alt="loading"> Loading ...',
        title: { button: true, text: ' ' },
        ajax: {
          url: '<?php echo url_for("import/extdinfo");?>',
          type: 'GET'
        }
      }
    });
  });
});
</script>
