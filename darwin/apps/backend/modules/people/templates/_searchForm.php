<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php
	$flagMenu=detect_menu_hidden();
?>
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
   	      
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $form['family_name']->render() ?></td>
          <td><?php echo $form['activity_date_from']->render() ?></td>
          <td><?php echo $form['activity_date_to']->render() ?></td>
          
          
        </tr>
        <tr>
            <th><?php echo $form['people_type']->renderLabel('Type');?></th>
            <th><?php echo $form['ig_number']->renderLabel('I.G. Number');?></th>
			<th></th>
        </tr>
        <tr>
            <td><?php echo $form['people_type']->render() ?></td>
            <td><?php echo $form['ig_number']->render() ?></td>
			<td></td>
        </tr>
		<tr>
			<th><?php echo $form['identifier']->renderLabel('Identifiers'); ?></th>
			<th></th>
			<th></th>
		</tr>
		<tr>
			<td colspan="2"><?php echo $form['protocol']->render() ?>&nbsp;<?php echo $form['identifier']->render() ?></td>
			<td></td>
			<td></td>
		</tr>
        <tr>
        <td colspan="4" style="text-align:right"><input class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" /></td>
        </tr>
      </tbody>
    </table>	
    <div class="search_results">
      <div class="search_results_content">
      </div>
    </div>
     <?php if($flagMenu): ?><div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for('people/new'). ($form['family_name']->getValue() ? '?name='.urlencode($form['family_name']->getValue()) :'') ; ?>"><?php echo __('New');?></a></div><?php endif;?>
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
  
  //ftheeten 2018 04 10
     var ig_num=urlParam('ig_num');
      if(!!ig_num)
      {
            
          $("#people_filters_ig_number").val(decodeURIComponent(ig_num));
          $( ".search_form" ).submit();
      }  

      var protocol=urlParam('identifier_protocol');
	  var identifier=urlParam('identifier_value');
      if(!!protocol&&!!identifier)
      {
          $( ".search_form" ).submit();
      }     	  
});
</script>
