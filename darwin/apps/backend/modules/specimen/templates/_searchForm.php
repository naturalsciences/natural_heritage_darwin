<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<?php echo form_tag('specimen/search'.( isset($is_choose) ? '?is_choose='.$is_choose : '') , array('class'=>'search_form'));?>

  <div class="container">
    <table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
      <thead>
        <tr>
          <th><?php echo $form['collection_name']->renderLabel() ?></th>
          <th><?php echo $form['taxon_name']->renderLabel() ?></th>
          <th><?php echo $form['taxon_level_ref']->renderLabel() ?></th>          
        </tr>
      </thead>    
      <tbody>
        <tr>
          <td><?php echo $form['collection_name']->render() ?></td>
          <td><?php echo $form['taxon_name']->render() ?></td>
          <td><?php echo $form['taxon_level_ref']->render() ?></td>    
        </tr>
      </tbody>
      <thead>
        <tr>
          <th><?php echo $form['code']->renderLabel() ?></th>
          <th><?php echo $form['ig_num']->renderLabel() ?></th>
          <th><?php echo $form['uuid']->renderLabel() ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $form['code']->render() ?></td>
          <td><?php echo $form['ig_num']->render() ?></td>
          <td><?php echo $form['uuid']->render() ?></td>
        </tr>
		<tr>
			<td colspan="3"><?php echo $form->renderHiddenFields();?><input class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" /></td>
		</tr>
      </tbody>      
    </table>
    <div class="search_results">
      <div class="search_results_content">
      </div>  
    </div>
  </div>
</form>
<script type="text/javascript">
 $(document).ready(function () {
  $('.host_search').choose_form({});
  

	
	$(".select2_code_values").autocomplete({
     
					source: function (request, response) {
						$.getJSON('<?php echo url_for('catalogue/codesAutocomplete?');?>', {
									term : request.term
								} , 
								function (data) 
									{
								response($.map(data, function (value, key) {
								return value;
								}));
						});
					},
					
					minLength: 2,
					delay: 100
				});
	
});
</script>
