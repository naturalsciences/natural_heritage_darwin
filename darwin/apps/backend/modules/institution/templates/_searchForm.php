<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php
	$flagMenu=detect_menu_hidden();
?>
<div class="catalogue_institutions">
  <?php echo form_tag('institution/search'.( isset($is_choose) ? '?is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'institution_filter'));?>
  <div class="container">
    <table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
      <thead>
        <tr>  
          <th><?php echo $form['family_name']->renderLabel('Institution Name');?></th>
		  <th><?php echo $form['identifier']->renderLabel('Identifiers'); ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $form['family_name'];?><?php echo $form['is_physical'];?></td>
		  <td><?php echo $form['protocol']->render() ?>&nbsp;<?php echo $form['identifier']->render() ?></td>
          <td><input class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" /></td>
        </tr>
      </tbody>
    </table>
    <div class="search_results">
      <div class="search_results_content"> 
      </div>
    </div>
     <?php if($flagMenu): ?><div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for('institution/new') ; ?>"><?php echo __('New');?></a></div><?php endif;?>
  </div>
</form> 
</div>
<script>
$(document).ready(function () {
  $('.catalogue_institutions').choose_form({});
  $(".new_link").click( function()
  {
   url = $(this).find('a').attr('href'),
   data= $('.search_form').serialize(),
   reg=new RegExp("(<?php echo $form->getName() ; ?>)", "g");   
   open(url+'?'+data.replace(reg,'institution'));
    return false;  
  });
  
   var protocol=urlParam('identifier_protocol');
   var identifier=urlParam('identifier_value');
   if(!!protocol&&!!identifier)
   {
		$( ".search_form" ).submit();
   }     	  
});
</script>
