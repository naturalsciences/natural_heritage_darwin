<?php slot('title', __('Import files summary'));  ?>

<div class="page">
<!--   <h1>Import will be available in 2014</h1> -->
 <div class="warn_message">The Import tool will be fully operational in 2014. This current beta version is already activated for testing purposes.<br />
 We strongly advise you, however, to patiently await the new release before you actively make use of it.</div>
<h1><?php echo __('Imports');?> : <?php echo image_tag('info.png',array('title'=>'info','class'=>'extd_info')); ?></h1>

    <?php include_partial('searchForm', array('form' => $form)) ?>
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
