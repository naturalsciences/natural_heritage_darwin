<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php echo form_tag('loan/search'.( isset($is_choose) ? '?is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'loans_filter'));?>
  <div class="container">
    <table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
      <thead>
        <tr>
          <!--ftheeten 2016 11 23 add collection ref
          <th><?php echo $form['collection_ref']->renderLabel() ?></th>-->
		  <!--jmHerpers 2018 03 20-->
          <th>Collection</th>
          <th><?php echo $form['name']->renderLabel() ?></th>
          <th><?php echo $form['status']->renderLabel() ?></th>
          <th><?php echo $form['from_date']->renderLabel() ?></th>
          <th><?php echo $form['to_date']->renderLabel() ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
            <!--ftheeten 2016 11 23 add collection ref-->
          <th><?php echo $form['collection_ref']->render() ?></th>
          <th><?php echo $form['name']->render() ?></th>
          <th><?php echo $form['status']->render() ?></th>
          <th><?php echo $form['from_date']->render() ?></th>
          <th><?php echo $form['to_date']->render() ?></th>
        </tr>
        <tr>
          <th><?php echo $form['people_ref']->renderLabel() ?></th>
          <th><?php echo $form['ig_ref']->renderLabel() ?></th>
          <th><?php echo $form['only_darwin']->renderLabel() ?></th>
        </tr>
        <tr>
          <th><?php echo $form['people_ref']->render() ?></th>
          <th><?php echo $form['ig_ref']->render() ?></th>
          <th><?php echo $form['only_darwin']->render() ?></th>
        </tr>
		<tr>
          <td rowspan="3" class="left_aligned">
			  <br><input class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" /> or
			  <div class='new_link'>
				 <a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for('loan/new') ; ?>"><?php echo __('New');?></a>
			  </div>
		  </td>
        </tr>
      </tbody>
    </table>
    <div class="search_results">
      <div class="search_results_content"> 
      </div>
    </div> 
  </div>
</form>
<script>
  $(document).ready(function ()
  {
    $('body').choose_form({content_elem: '.search_results_content' });
    $('#loans_filters_ig_ref_name').bind('blur',function (event) {
      $(this).removeClass('complete_missing');
    }).bind('missing',function (event) {
      $(this).addClass('complete_missing');
    });
  });
</script>
