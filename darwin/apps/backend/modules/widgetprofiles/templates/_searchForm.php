<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<div class="catalogue_users">
<?php echo form_tag('widgetprofiles/search'.( isset($is_choose) ? '?is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'users_filter'));?>
  <div class="container">
      <table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
		<thead>
			<tr>
				<th><?php echo $form['name']->renderLabel('Name') ?></th>
				<th><?php echo $form['creator_ref']->renderLabel('Creator') ?></th>
				<th></th>
			</tr>
		</thead>

	  <tbody>
		<tr>
          <td><?php echo $form['name']->render() ?></td>
		  <td><?php echo $form['creator_ref']->render() ?></td>
		  <td></td>
		</tr>
		<tr>
        <td style="text-align:left"><input class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" /></td>
        </tr>
	  </tbody>
	 </table>
      <div class="search_results">
		
      <div class="search_results_content"> 
      </div>
    </div> 
  </div>
</form> 
</div>
<script language="javascript">
$(document).ready(function () {
  $('.catalogue_users').choose_form({});
});
</script>