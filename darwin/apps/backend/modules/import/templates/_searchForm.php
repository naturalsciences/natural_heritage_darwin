<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
  <div class="import_filter">
  <?php 
		//ftheeten 2018 07 15
		if($format=="taxon")
		{
			$path='import/searchCatalogue';
			
		}
		elseif($format=="abcd")
		{
			$path='import/search';
			
		}
		elseif($format=="files")
		{
			$path='import/searchFiles';
			
		}
		elseif($format=="links")
		{
			$path='import/searchLinks';
			
		}
		print(form_tag($path, array('class'=>'search_form','id'=>'import_filter')));?>
  <div class="container">
    <table class="search" id="search">
      <thead>
        <tr>
          <th><?php if($format != 'taxon') echo $form['collection_ref']->renderLabel() ; ?></th>
          <th><?php echo $form['filename']->renderLabel() ?></th>
          <th><?php echo $form['state']->renderLabel(); ?></th>
          <th><?php echo $form['show_finished']->renderLabel(); ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php if($format != 'taxon') echo $form['collection_ref']->render() ; ?></td>
          <td><?php echo $form['filename']->render() ?></td>
          <td><?php echo $form['state']->render() ?></td>
          <th><?php echo $form['show_finished']->render(); ?>
          </th>   
          <td><input class="search_submit" type="submit" name="search" value="<?php echo __('Filter'); ?>" /></td>
        </tr>
      </tbody>
    </table>
    <div class="search_results">
      <div class="search_results_content">
      </div>      
    </div>
    <?php if($format == 'taxon') : ?>    
      <div class="new_link"><a href="<?php echo url_for('import/upload?format=taxon') ?>"><?php echo __('Import Taxons');?></a>
	<?php elseif($format == 'files') : ?>    
      <div class="new_link"><a href="<?php echo url_for('import/upload?format=files') ?>"><?php echo __('Import Files');?></a>
	<?php elseif($format == 'links') : ?>    
      <div class="new_link"><a href="<?php echo url_for('import/upload?format=links') ?>"><?php echo __('Import Links');?></a>
    <?php else : ?>
      <div class="new_link"><a href="<?php echo url_for('import/upload?format=abcd') ?>"><?php echo __('Import Specimens');?></a></div>
    <?php endif ; ?>
    </div>
  </div>
</form>  
  </div>

<script type="text/javascript">
 $(document).ready(function () {
  $('.import_filter').choose_form({});
});
</script>
