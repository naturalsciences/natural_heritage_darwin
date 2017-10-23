<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<div class="catalogue_properties">
<?php echo form_tag('property/search', array('class'=>'search_form','id'=>'property_filter'));?>
  <div class="container">
<table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
  <thead>
    <tr>
      <th ><?php echo $form['referenced_relation']->renderLabel(); ?>
        <?php echo $form['referenced_relation']->renderError();?></th>
      <th ><?php echo $form['property_type']->renderLabel(); ?>
        <?php echo $form['property_type']->renderError();?></th>
      <th colspan="2"><?php echo $form['applies_to']->renderLabel(); ?>
        <?php echo $form['applies_to']->renderError();?></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php echo $form['referenced_relation'];?></td>
      <td><?php echo $form['property_type'];?></td>
      <td colspan="2"><?php echo $form['applies_to'];?></td>
    </tr>
    <tr>
      <th><?php echo $form['lower_value']->renderLabel(); ?>
        <?php echo $form['lower_value']->renderError();?></th>
      <th><?php echo $form['upper_value']->renderLabel(); ?>
        <?php echo $form['upper_value']->renderError();?></th>
      <th><?php echo $form['property_unit']->renderLabel(); ?>
        <?php echo $form['property_unit']->renderError();?></th>
      <th></th>
    </tr>
    <tr>
      <td><?php echo $form['lower_value'];?></td>
      <td><?php echo $form['upper_value'];?></td>
      <td><?php echo $form['property_unit'];?></td>
      <td><input class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" /></td>
    </tr>
  </tbody>
</table>

    <div class="search_results">
      <div class="search_results_content">
      </div>
    </div>
  </div>
</form>
<div>
<script>
$(document).ready(function () {
  $('.catalogue_properties').choose_form({});
});
</script>
