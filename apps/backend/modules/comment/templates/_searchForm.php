<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<div class="catalogue_comments">
<?php echo form_tag('comment/search', array('class'=>'search_form','id'=>'comments_filter'));?>
  <div class="container">
<table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
  <thead>
    <tr>
      <th ><?php echo $form['referenced_relation']->renderLabel(); ?>
        <?php echo $form['referenced_relation']->renderError();?></th>
      <th ><?php echo $form['comment']->renderLabel(); ?>
        <?php echo $form['comment']->renderError();?></th>
      <th><?php echo $form['notion_concerned']->renderLabel(); ?>
        <?php echo $form['notion_concerned']->renderError();?>
      </th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php echo $form['referenced_relation'];?></td>
      <td><?php echo $form['comment'];?></td>
      <td><?php echo $form['notion_concerned'];?></td>
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
  $('.catalogue_comments').choose_form({});
});
</script>
