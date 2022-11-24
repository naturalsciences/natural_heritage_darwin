<tr class="tag_content_line_donator_<?php echo($row_line);?>">
      <td class="precise_donator_<?php echo($row_line);?>" colspan="2">
	  <?php echo $form['people_ref'];?> 
	
	  
	  </td>
				
			<td class="widget_row_delete">

					<?php echo image_tag('remove.png', 'alt=Delete class=clear_prop id=clear_tag_donator_'.$row_line); ?>
				</td>
			</tr>
</tr>

<script  type="text/javascript">
  $(document).ready(function () {
  
	$('#mass_action_MassActionForm_donators_Peoples_<?php echo($row_line);?>_people_ref').change(
		
		function()
		{
			
			changeSubmit(true);
		});
		
		  $('#clear_tag_donator_<?php echo $row_line;?>').click(function(){
			if($(this).closest('tbody').find('tr.tag_content_line_donator_<?php echo($row_line);?>').length == 1)
			{
				if($(this).closest('tbody').find('tr.tag_button_line_donator_<?php echo($row_line);?>').length == 1)
				{
					$('.tag_button_line_donator_<?php echo($row_line);?>').remove();
				}
				if($(this).closest('tbody').find('tr.tag_header_line_donator_<?php echo($row_line);?>').length == 1)
				{
					$('.tag_header_line_donator_<?php echo($row_line);?>').remove();
				}
			  $('.tag_content_line_donator_<?php echo($row_line);?>').remove();
			  
			 
			}
			else
			  $(this).closest('tr').remove();
		  });
   });
</script>