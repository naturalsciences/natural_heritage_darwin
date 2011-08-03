<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

  <?php echo form_tag('import/search', array('class'=>'search_form','id'=>'import_filter'));?>
  <div class="container">
    <table class="search" id="search">
      <thead>
        <tr>
          <th><?php echo $form['collection_ref']->renderLabel() ?></th>
          <th><?php echo $form['filename']->renderLabel() ?></th>
          <th><?php echo $form['state']->renderLabel(); ?></th>
          <th><?php echo $form['show_finished']->renderLabel(); ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $form['collection_ref']->render() ?></td>
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
    <div class="new_link"><a href="<?php echo url_for('import/upload') ?>"><?php echo __('Import a file');?></a>
    </div>
  </div>
</form>  

