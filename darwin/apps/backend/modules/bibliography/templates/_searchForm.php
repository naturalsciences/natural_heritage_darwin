<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php
	$flagMenu=detect_menu_hidden();
?>
<div class="catalogue_bibliography">
<?php echo form_tag('bibliography/search'.( isset($is_choose) ? '?is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'bibliography_filter'));?>
  <div class="container">
    <table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
      <thead>
        <tr>
          <th><?php echo $form['type']->renderLabel() ?></th>
          <th><?php echo $form['title']->renderLabel() ?></th>
		  <th><?php echo $form['author_name']->renderLabel() ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $form['type']->render() ?></td>
          <td><?php echo $form['title']->render() ?></td>
		  <td><?php echo $form['author_name']->render() ?></td>
		</tr>
		<tr>
		  <th><?php echo $form['reference']->renderLabel() ?></th>
          <th><?php echo $form['uri_protocol']->renderLabel() ?> <?php echo $form['uri']->renderLabel() ?></th>
          <th><?php echo $form['year']->renderLabel() ?></th>
        </tr>
        <tr>
		  <td><?php echo $form['reference']->render() ?></td>
          <td><?php echo $form['uri_protocol']->render() ?> <?php echo $form['uri']->render() ?></td>
          <td><?php echo $form['year']->render() ?></td>
        </tr>		
		<tr>
          <td><input class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" /></td>
        </tr>
      </tbody>
    </table>
    <div class="search_results">
      <div class="search_results_content">
      </div>  
    </div>
	<?php if($flagMenu):?>
    <div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for('bibliography/new?name='.$form['title']->getValue()) ?>"><?php echo __('New');?></a></div>
	<?php endif;?>
  </div>
</form>
</div>
<script>
$(document).ready(function () {
  $('.catalogue_bibliography').choose_form({});

  $(".new_link").click( function()
  {
   url = $(this).find('a').attr('href'),
   data= $('.search_form').serialize(),
   reg=new RegExp("(<?php echo $form->getName() ; ?>)", "g");   
   open(url+'?'+data.replace(reg,'bibliography'));
    return false;  
  });
});
</script>
