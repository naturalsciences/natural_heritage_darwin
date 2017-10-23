

  <tr class="tag_button_line_people_<?php echo($row_line);?>">
      <td colspan="2">
        <input type="button" id='people_switch_precise_<?php echo($row_line);?>' value="<?php echo __('Precise search'); ?>" disabled>
        <input type="button" id='people_switch_fuzzy_<?php echo($row_line);?>' value="<?php echo __('Fuzzy search'); ?>">
      </td>
    </tr>
    <tr class="tag_header_line_people_<?php echo($row_line);?>">
      <th colspan="2" class="precise_people_<?php echo($row_line);?>"><?php echo $form['people_ref']->renderLabel();?></th>
	  <th  colspan="2"class="fuzzy_people_<?php echo($row_line);?> hidden"><?php echo $form['people_fuzzy']->renderLabel();?></th>
      <th><?php echo $form['role_ref']->renderLabel();?></th>
    </tr>
 

    <tr class="tag_content_line_people_<?php echo($row_line);?>">
      <td class="precise_people_<?php echo($row_line);?>" colspan="2"><?php echo $form['people_ref'];?></td>
	  <td class="fuzzy_people_<?php echo($row_line);?> hidden" colspan="2"><?php echo $form['people_fuzzy'];?></td>
      <td><?php echo $form['role_ref'];?></td> 
	  <td class="widget_row_delete">
			<?php echo image_tag('remove.png', 'alt=Delete class=clear_prop id=clear_tag_people_'.$row_line); ?>
		</td>
    </tr>


<script  type="text/javascript">
  $(document).ready(function () {
  $('#people_switch_precise_<?php echo($row_line);?>').click(function() {

    $('#people_switch_precise_<?php echo($row_line);?>').attr('disabled','disabled') ;
    $('.fuzzy_people_<?php echo($row_line);?>').removeAttr('disabled') ;
    $('.precise_people_<?php echo($row_line);?>').toggle() ;
    $(this).closest('table').find('.people_switch_fuzzy_<?php echo($row_line);?>').toggle() ;
   
	check_state();
	// $('#specimen_search_filters_Peoples_<?php echo($row_line);?>_people_ref_name').html("") ;
    // $('#specimen_search_filters_Peoples_<?php echo($row_line);?>_people_ref').val("") ;
  });

  $('#people_switch_fuzzy_<?php echo($row_line);?>').click(function() {

    $('#people_switch_fuzzy_<?php echo($row_line);?>').removeAttr('disabled') ;
    //$('.fuzzy_people_<?php echo($row_line);?>').attr('disabled','disabled') ;
    $('.precise_people_<?php echo($row_line);?>').toggle() ;
    $(this).closest('table').find('.fuzzy_people_<?php echo($row_line);?>').toggle() ;
    $('.fuzzy_people_<?php echo($row_line);?>').find('input:text').val("") ;
	check_state();
  });
 
   if($('.class_fuzzy_people_<?php echo($row_line);?>').val() != '')
  {
	tmpVal=$('.class_fuzzy_people_<?php echo($row_line);?>').val();
    $('#people_switch_fuzzy_<?php echo($row_line);?>').trigger("click") ;
	$('.class_fuzzy_people_<?php echo($row_line);?>').val(tmpVal);
  }

 
});

  function check_state()
  {
			
			if(($(".fuzzy_people_<?php echo($row_line);?>").is(":visible")))
			{

				var valTmp=$('#specimen_search_filters_Peoples_<?php echo($row_line);?>_people_ref_name').text();
				if(valTmp.length>0)
				{
					$('.fuzzy_people_<?php echo($row_line);?>').find('input:text').val(valTmp);
					$('#specimen_search_filters_Peoples_<?php echo($row_line);?>_people_ref_name').html("") ;
					$('#specimen_search_filters_Peoples_<?php echo($row_line);?>_people_ref').val("") ;
				}
			}
			else if(($(".precise_people_<?php echo($row_line);?>").is(":visible")))
			{

				$('.fuzzy_people_<?php echo($row_line);?>').find('input:text').val("");
			}
  }
  
  $('#clear_tag_people_<?php echo $row_line;?>').click(function(){
    if($(this).closest('tbody').find('tr.tag_content_line_people_<?php echo($row_line);?>').length == 1)
    {
		if($(this).closest('tbody').find('tr.tag_button_line_people_<?php echo($row_line);?>').length == 1)
		{
			$('.tag_button_line_people_<?php echo($row_line);?>').remove();
		}
		if($(this).closest('tbody').find('tr.tag_header_line_people_<?php echo($row_line);?>').length == 1)
		{
			$('.tag_header_line_people_<?php echo($row_line);?>').remove();
		}
      $('.tag_content_line_people_<?php echo($row_line);?>').remove();
	  
	 
    }
    else
      $(this).closest('tr').remove();
  });
</script>
