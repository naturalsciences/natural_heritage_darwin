<li alt="<?php echo $form['group_name']->getValue();?>">
    <?php echo $form['id'];?>
    <?php echo $form['group_name'];?>
  <div class="sub_group">
    <?php echo $form['id']->renderError(); ?>
    <?php echo $form['group_name']->renderError(); ?>

    <?php echo $form['sub_group_name']->renderError(); ?>
    <?php echo $form['sub_group_name'];?>
  </div>

  <div class="tag_encod">
    <?php echo $form['international_name']->renderError(); ?>
    <?php echo $form['international_name'];?>

    <?php echo $form['tag_value']->renderError(); ?>
    <?php echo $form['tag_value'];?>

    <div class="purposed_tags">
    </div>
  </div>

  <div class="widget_row_delete">
    <?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?>
  </div>
</li>
