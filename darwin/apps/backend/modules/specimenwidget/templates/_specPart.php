<table id="table_part">
	<tr>
  <th class="top_aligned"><?php echo $form['category']->renderLabel();?></th>
  <td>
    <?php echo $form['category']->renderError();?>
    <?php echo $form['category']->render() ?>
  </td>
  </tr>
  <tr>
  <th class="top_aligned"><?php echo $form['specimen_part']->renderLabel();?></th>
  <td>
    <?php echo $form['specimen_part']->renderError();?>
    <?php echo $form['specimen_part']->render() ?>
  </td>
  </tr>  
  <tr>
  <th class="top_aligned"><?php echo $form['object_name']->renderLabel();?></th>
  <td>
    <?php echo $form['object_name']->renderError();?>
    <?php echo $form['object_name']->render() ?>
  </td>
  </tr>
</table>
<p class="form_buttons" style="text-align:right;">
<a href="<?php echo url_for('specimen/new?duplicate_id='.$form->getObject()->getId().'&part_id='.$form->getObject()->getId());?>" class="duplicate_link"><?php echo __('Split into parts');?></a>
</p>
<script  type="text/javascript">
$(document).ready(
		function()
		{
	<?php if(strpos($_SERVER['REQUEST_URI'],'/part_id/')):?>
		
			var partElem=$("#specimen_specimen_part_parent").find('.add_item_button')[0];
			if(partElem)
			{
				$(partElem).click();
				
			}
			
			$(window).scrollTop($('#table_part').offset().top-300);		
		
	<?php endif;?>
		//2025 12 23
		
	    var get_specimen_part=function(p_val, p_default)
		{
			$.get("<?php echo url_for('specimen/GetSpecimenPart');?>/item/category/type/"+p_val, function (data) {
			  data=data.replace('value="'+p_default+'"', 'value="'+p_default+'" selected');
              $('select[name="specimen[specimen_part]"]').html(data);
				});
			
		}
		
		/*var init=function()
		{
			var init_cat=$('select[name="specimen[category]"]').val();
			get_specimen_part(init_cat);
			
		}*/
		$('select[name="specimen[category]"]').change(function() {
			
			get_specimen_part($(this).val(), "<?php print($default_part);?>");
		});	
		
		//init();
		
		
	});
</script>

