<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<div  class="catalogue_expeditionigs">
<?php echo form_tag('expeditionsIgs/search' , array('class'=>'search_form','id'=>'expeditionsIgs_filter'));?>
  <div class="container">
    <table class="search" id="search">
      <thead>
        <tr>
          <th><?php echo $form['ig_ref']->renderLabel() ?></th>
          <th><?php echo $form['expedition_name']->renderLabel(); ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $form['ig_ref']->render() ?></td>
          <td><?php echo $form['expedition_name']->render() ?></td>
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
</div>
<script type="text/javascript">
 $(document).ready(function () {
  $('.catalogue_expeditionigs').choose_form({});
  $('#searchExpeditionIgs_ig_ref_name').bind('focus',function (event) {
    $(this).removeClass('complete_missing');
  }).bind('missing',function (event) {
    $(this).addClass('complete_missing');
  });

 });
</script>
