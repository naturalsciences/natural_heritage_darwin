<tr>
  <td>
  <table>
   <tr>
      <td><?php echo $code['category']->renderError();?></td>
      <td><?php echo $code['code_part']->renderError();?></td>
      <td></td>
      <td><?php echo $code['code_from']->renderError();?></td>
      <td><?php echo $code['code_to']->renderError();?></td>
      <td></td>
    </tr>
  <thead>
    <tr class="line_header_code">
      <th><?php echo __('Category');?></th>
      <th colspan="2" class="standard_code_col"><?php echo __('Code');?></th>
      <th class="between_col"><?php echo __('Prefix');?></th>
      <th class="between_col"><?php echo __('Between');?></th>
      <th class="between_col"><?php echo __('and');?></th>
      <th></th>
    </tr>
    <thead>
    <tbody>
   
    <tr>
      <td><?php echo $code['category'];?></td>
      <td><?php echo $code['code_part'];?></td>
      <td class="and_col">
        <?php echo link_to(image_tag('next.png'),'specimen/index', array('class'=>'code_between next'));?>
        <?php echo link_to(image_tag('previous.png'),'specimen/index', array('class'=>'code_between hidden prev'));?>
      </td>
      <td class="between_col"><?php echo $code['code_prefix'];?></td>
      <td class="between_col"><?php echo $code['code_from'];?></td>
      <td class="between_col"><?php echo $code['code_to']->renderError();?><?php echo $code['code_to'];?></td>
      <td>
        <?php echo image_tag('remove.png', 'alt=Delete class=clear_prop id=clear_code_'.$row_line); ?>
      </td>
    </tr>
    <td>
        <?php echo $code['exclude_prefix_in_searches']->renderLabel();?>
    </td>
    <td>
        <?php echo $code['exclude_prefix_in_searches'];?>
    </td>
    </tr>
   </tbody>
  </table>
 </td>
</tr>
<script  type="text/javascript">
  $('#clear_code_<?php echo $row_line;?>').click(function(event)
  {
    event.preventDefault();
    if($(this).closest('tbody').find('tr').length == 3)
    {
      $(this).closest('tr').find('td input').val('');
    }
    else
    {
      other_row = $(this).closest('tr').prev();
      $(this).closest('tr').remove();
      other_row.remove();
    }
    checkBetween();
  });
  
    //ftheeten 2018 03 08
  var url_code="<?php echo(url_for('catalogue/codesAutocomplete?'));?>";
  var autocomplete_rmca_array=Array();
  $('.autocomplete_for_code').autocomplete({
		source: function (request, response) {
			$.getJSON(url_code, {
						term : request.term,
						collections: autocomplete_rmca_array.join()
					} , 
					function (data) 
						{
					response($.map(data, function (value, key) {
					return value;
                    }));
			});
		},
		minLength: 2,
		delay: 200
	});
</script>

