<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<div class="catalogue_multimedia">
<?php echo form_tag('multimedia/search', array('class'=>'search_form','id'=>'multimedia_filter'));?>
  <div class="container">
<table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
  <thead>
    <tr>
      <th ><?php echo $form['referenced_relation']->renderLabel(); ?>
        <?php echo $form['referenced_relation']->renderError();?></th>
      <th ><?php echo $form['title']->renderLabel(); ?>
        <?php echo $form['title']->renderError();?></th>
      <th><?php echo $form['type']->renderLabel(); ?>
        <?php echo $form['type']->renderError();?>
      </th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php echo $form['referenced_relation'];?></td>
      <td><?php echo $form['title'];?></td>
      <td><?php echo $form['type'];?></td>
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
  $('.catalogue_multimedia').choose_form({});
});
</script>
