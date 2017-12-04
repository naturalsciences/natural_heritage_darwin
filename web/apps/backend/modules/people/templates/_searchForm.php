<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<div class="catalogue_people">
<?php echo form_tag('people/search'.( isset($is_choose) ? '?is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'people_filter'));?>
  <div class="container">
    <?php echo $form['is_physical'];?>
    <table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
      <thead>
        <tr>
          <th><?php echo $form['family_name']->renderLabel('Name') ?></th>
          <th><?php echo $form['activity_date_from']->renderLabel(); ?></th>
          <th><?php echo $form['activity_date_to']->renderLabel(); ?></th>
   	      <th><?php echo $form['people_type']->renderLabel('Type');?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $form['family_name']->render() ?></td>
          <td><?php echo $form['activity_date_from']->render() ?></td>
          <td><?php echo $form['activity_date_to']->render() ?></td>
          <td><?php echo $form['people_type']->render() ?></td>
          <td><input class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" /></td>
        </tr>
      </tbody>
    </table>
    <div class="search_results">
      <div class="search_results_content">
      </div>
    </div>
    <div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for('people/new'). ($form['family_name']->getValue() ? '?name='.urlencode($form['family_name']->getValue()) :'') ; ?>"><?php echo __('New');?></a></div>
  </div>
</form>
<div>
<script>
$(document).ready(function () {
  $('.catalogue_people').choose_form({});
  $(".new_link").click( function()
  {
   url = $(this).find('a').attr('href'),
   data= $('.search_form').serialize(),
   reg=new RegExp("(<?php echo $form->getName() ; ?>)", "g");
   open(url+'?'+data.replace(reg,'people'));
    return false;
  });
});
</script>
