<li alt="<?php echo $form['group_name']->getValue();?>">
    <?php echo $form['id'];?>
    <?php echo $form['group_name'];?>
  <div class="sub_group"> <!--combobox continent, country,...-->
    <?php echo $form['id']->renderError(); ?>
    <?php echo $form['group_name']->renderError(); ?>

    <?php echo $form['sub_group_name']->renderError(); ?>
    <?php echo $form['sub_group_name'];?>
  </div>

  <div class="tag_encod">  <!-- field at right of combobox for value -->
    <!--ftheeten 2018 03 05 hide international name-->
    <!--<?php echo $form['international_name']->renderError(); ?>
    <?php echo $form['international_name'];?>-->

    <?php echo $form['tag_value']->renderError(); ?>    <!-- 'iso3166_text' replaced by  'tag_value'-->
    <?php echo $form['tag_value'];?>

	 <!--////////////////////////////
       
			<php echo __("ISO 3166") ?>
			<php echo $form['iso3166']->renderError() ?>
			<php echo $form['iso3166'] ?>

	//////////////////////////////-->
    <div class="purposed_tags">
    </div>
  </div>

  <div class="widget_row_delete">
    <?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?>
  </div>
</li>