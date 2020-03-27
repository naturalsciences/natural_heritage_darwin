<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php
	$flagMenu=detect_menu_hidden();
?>
<div class="catalogue_expedition">
<?php echo form_tag('expedition/search'.( isset($is_choose) ? '?is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'expedition_filter'));?>
  <div class="container">
  <div  style="text-align:right"><input class="search_submit get_tab" type="button" name="search" value="<?php echo __('Get tab-delimited'); ?>" /></div>
    <table class="search" style="width:auto" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
      <thead>
        <tr>
          <th><?php echo $form['name']->renderLabel() ?></th>
          <th><?php echo $form['expedition_from_date']->renderLabel(); ?></th>
          <th><?php echo $form['expedition_to_date']->renderLabel(); ?></th>
		  <th><?php echo __('ig_num'); ?></th>
          
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $form['name']->render() ?></td>
          <td><?php echo $form['expedition_from_date']->render() ?></td>
          <td><?php echo $form['expedition_to_date']->render() ?></td>
		  <td><?php echo $form['ig_ref']->render() ?></td>
          
       </tr>
       <tr>
          <td colspan="2"></td>
          <td style="text-align:left" colspan="2"><input class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" /></td>
        </tr>
      </tbody>
    </table>
    <div class="search_results">
      <div class="search_results_content">
      </div>  
    </div>
   <?php if($flagMenu): ?> <div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for('expedition/new?name='.$form['name']->getValue()) ?>"><?php echo __('New');?></a></div><?php endif;?>
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
  
  $(".get_tab").click(
	function()
	{
		

		var $tmp=$('form:first');
		
		var new_target="<?php echo url_for('expedition/downloadTab') ?>";		
		var $inputs = $('form:first :input');
        var form = document.createElement("form");
		form.hidden=true;
		form.setAttribute("method", "post");
		form.setAttribute("action", new_target);

		form.setAttribute("target", "view");

		
		$inputs.each(function() {
			var hiddenField = document.createElement("input"); 
			
			hiddenField.setAttribute("name", this.name);
			hiddenField.setAttribute("value", $(this).val());
			
			form.appendChild(hiddenField);
		});
		
		document.body.appendChild(form);

		window.open('', 'view');

		form.submit();
	}
    );
});
</script>
