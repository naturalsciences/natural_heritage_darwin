<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<div class="catalogue_expedition">
<?php echo form_tag('expedition/search'.( isset($is_choose) ? '?is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'expedition_filter'));?>
  <div class="container">
    <table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
      <thead>
        <tr>
          <th><?php echo $form['name']->renderLabel() ?></th>
          <th><?php echo $form['expedition_from_date']->renderLabel(); ?></th>
          <th><?php echo $form['expedition_to_date']->renderLabel(); ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $form['name']->render() ?></td>
          <td><?php echo $form['expedition_from_date']->render() ?></td>
          <td><?php echo $form['expedition_to_date']->render() ?></td>
          <td><input class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" /></td>
        </tr>
      </tbody>
    </table>
    <div class="search_results">
      <div class="search_results_content">
      </div>  
    </div>
    <div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for('expedition/new?name='.$form['name']->getValue()) ?>"><?php echo __('New');?></a></div>
  </div>
</form>
</div>
<script>
$(document).ready(function () {
  $('.catalogue_expedition').choose_form({});

  $(".new_link").click( function()
  {
   url = $(this).find('a').attr('href'),
   data= $('.search_form').serialize(),
   reg=new RegExp("(<?php echo $form->getName() ; ?>)", "g");   
   open(url+'?'+data.replace(reg,'expedition'));
    return false;  
  });
});
</script>
