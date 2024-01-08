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
<?php if(strpos($_SERVER['REQUEST_URI'],'/part_id/')):?>
		$(document).ready(
		function()
		{
			var partElem=$("#specimen_specimen_part_parent").find('.add_item_button')[0];
			if(partElem)
			{
				$(partElem).click();
				
			}
			
			$(window).scrollTop($('#table_part').offset().top-300);
		}
		);
	<?php endif;?>
</script>

