<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php
	$flagMenu=detect_menu_hidden();
?>
<div class="catalogue_georeferences_by_service">
<?php echo form_tag('georeferences/search'.( isset($is_choose) && $is_choose  ? '?is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'georeferences_filter'));?>
  <div class="container">
    <table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
      <thead>       
        
        <tr>
        
          <th><?php echo $form['data_origin']->renderLabel() ?></th>
          <th><?php echo $form['name']->renderLabel(); ?></th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>		 
          <td><?php echo $form['data_origin']->render() ?></td>
          <td><?php echo $form['name']->render() ?></td>
          <td></td>
          <td></td>
        </tr>
	</tbody>
     </table>	
      <fieldset id="lat_long_set">
     </fieldset>
      <?php echo $form->renderHiddenFields();?>
      <div class="edit">
        <input class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" />
      </div>
      <div class="clear"></div>
       <script>
	      $(document).ready(function () {
			    $('.catalogue_georeferences_by_service').choose_form({});
		  });
	   </script>
      <div class="search_results">
        <div class="search_results_content"></div>
      </div>
      <?php if($flagMenu):?><div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for('georeferences/new') ?>"><?php echo __('New');?></a></div><?php endif;?>
  </div>
</form> 
</div>
