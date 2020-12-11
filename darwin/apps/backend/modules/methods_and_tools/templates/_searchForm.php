<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php
	$flagMenu=detect_menu_hidden();
?>
<div class="catalogue_methods">
<?php if(isset($notion) && (($notion == 'method' || $notion =='tool'))):?>
  <?php echo form_tag('methods_and_tools/search?notion='.$notion.( isset($is_choose) ? '&is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'methods_and_tools_filter'));?>
    <div class="container">
    <div  style="text-align:right"><input class="search_submit get_tab" type="button" name="search" value="<?php echo __('Get tab-delimited'); ?>" /></div>
      <table class="search " id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
        <thead>
          <tr>
            <th><?php echo $form[$notion]->renderLabel() ?></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?php echo $form[$notion]->render() ?></td>
            <td><input class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" /></td>
          </tr>
        </tbody>
      </table>
      <div class="search_results">
        <div class="search_results_content">
        </div>
      </div>
      <?php if ($sf_user->isAtleast(Users::ENCODER)&&$flagMenu) : ?>        
      <div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for('methods_and_tools/new?notion='.$notion) ?>"><?php echo __('New');?></a></div>
      <?php endif ; ?>
    </div>
  </form>
<?php else:?>
  <?php echo __('You need to specify if you wish to work on tools or methods');$notion="";?>
<?php endif;?>
</div>
<script language="javascript">
$(document).ready(function () {
  $('.catalogue_methods').choose_form({});
  $('form#methods_and_tools_filter').submit();
  
    $(".get_tab").click(
	function()
	{
		
   
		var $tmp=$('form:first');
		
		var new_target="<?php echo url_for('methods_and_tools/downloadTab') ?>";		
		var $inputs = $('form:first :input');
        var form = document.createElement("form");
		form.hidden=true;
		form.setAttribute("method", "post");
		form.setAttribute("action", new_target);
        
		form.setAttribute("target", "view");
        var hiddenField = document.createElement("input"); 			
	    hiddenField.setAttribute("name", "notion");
	    hiddenField.setAttribute("value", "<?php print($notion);?>");
      
	    form.appendChild(hiddenField);
        
       $inputs.each(function() {
			var hiddenField = document.createElement("input"); 
			
			hiddenField.setAttribute("name", this.name);
			hiddenField.setAttribute("value", $(this).val());
			
			form.appendChild(hiddenField);
		});
		
		
		
		document.body.appendChild(form);

		window.open('', 'view');
console.log( "SUBMI");   
		form.submit();
	}
    );
});
</script>
