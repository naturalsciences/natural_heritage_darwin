<table class="catalogue_table_view">
  <tbody>
    <tr>
      <td colspan="2">
        <?php echo image_tag('info.png',array(
          'title'=>'info',
          'class'=>'coll_extd_info',
          'data-manid'=> $spec->getCollections()->getMainManagerRef(),
          'data-staffid'=> $spec->getCollections()->getStaffRef())
          );?>
        <?php echo $spec->getCollections()->getName(); ?>
      </td>
    </tr>
    <tr>
      <th><?php echo __("Specimen category ") ?></th>
      <td>
       <?php echo $spec->getCategory() ; ?>
      </td>
    </tr>
  </tbody>
</table>
<script type="text/javascript">
$(document).ready(function () {
  $(".coll_extd_info").qtip({
    show: { solo: true, event:'click' },
    hide: { event:false },
    style: 'ui-tooltip-light ui-tooltip-rounded ui-tooltip-dialogue',
    content: {
      text: '<img src="/images/loader.gif" alt="loading"> Loading ...',
      title: { button: true, text: ' ' },
      ajax: {
        url: '<?php echo url_for('collection/extdinfo');?>',
        type: 'GET',
        data: { id: $(".coll_extd_info").attr('data-manid'), staffid: $(".coll_extd_info").attr('data-staffid')}
      }
    }
  });
});
</script>
