<div class="page">

  <div class="warn_message">
    <p>
      <?php echo __('The record that you are trying to delete has children. If you delete it, every children will be deleted too!');?>
    </p>
    <br />
    <span class="remove_confirm">
      <?php echo image_tag('remove.png'); ?>
      <?php echo link_to("Delete it anyway",sfOutputEscaper::unescape($link_delete));?>
    </span>
    <?php echo link_to("Cancel",sfOutputEscaper::unescape($link_cancel));?>
    <br /><br />
  </div>
</div>
