<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<div class="catalogue_taxonomymetadata">
<?php echo form_tag('taxonomymetadata/search'.( isset($is_choose) ? '?is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'taxonomymetadata_filter'));?>
  <div class="container">
    <table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
      <thead>
        <tr>
          <th><?php echo $form['taxonomy_idx']->renderLabel() ?></th>
          <th><?php echo $form['is_reference_taxonomy']->renderLabel(); ?></th>
          <th><?php echo $form['creation_date_from']->renderLabel(); ?></th>
          <th><?php echo $form['creation_date_to']->renderLabel(); ?></th>
         
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $form['taxonomy_idx']->render() ?></td>
          <td><?php echo $form['is_reference_taxonomy']->render() ?></td>
          <td><?php echo $form['creation_date_from']->render(); ?></td>
          <td><?php echo $form['creation_date_to']->render(); ?></td>
         <td><input class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" /></td>
        </tr>
      </tbody>
    </table>
    <div class="search_results">
      <div class="search_results_content">
      </div>  
    </div>
    <div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for('taxonomymetadata/new?name='.$form['taxonomy_name']->getValue()) ?>"><?php echo __('New');?></a></div>
  </div>
</form>
</div>
<script>
$(document).ready(function () {
  $('.catalogue_taxonomymetadata').choose_form({});

  $(".new_link").click( function()
  {
   url = $(this).find('a').attr('href'),
   data= $('.search_form').serialize(),
   reg=new RegExp("(<?php echo $form->getName() ; ?>)", "g");   
   open(url+'?'+data.replace(reg,'taxonomymedata'));
    return false;  
  });
});
</script>
