<tr class="tag_line">
  <td colspan="3">
    <?php echo $form['tag'];?>
    <div class="purposed_tags" id="purposed_tags_<?php echo $row_line;?>"></div>
  </td>
  <td class="top_aligned">
    <input type="button" value="<?php echo __("fuzzy associations");?>" name="btn_fuz_<?php echo($row_line);?>" id="btn_fuz_<?php echo($row_line);?>" class="result_choose"/>
  </td>
  <td class="widget_row_delete">
    <?php echo image_tag('remove.png', 'alt=Delete class=clear_prop id=clear_tag_'.$row_line); ?>
  </td>
</tr>
<script  type="text/javascript">
  $('#btn_fuz_<?php echo $row_line ; ?>').on('click',purposeTagsViaButton);
  $('input.tag_line_<?php echo $row_line ; ?>').on('keydown click',purposeTags);
  $('#clear_tag_<?php echo $row_line;?>').click(function(){
    if($(this).closest('tbody').find('tr.tag_line').length == 1)
    {
      $(this).closest('tr').find('td input:first').val('');
    }
    else
      $(this).closest('tr').remove();
  });

  function purposeTags(event)
  {
    if (event.type == 'keydown')
    {
      var code = (event.keyCode ? event.keyCode : event.which);
      if (code != 59 /* ;*/ && code != $.ui.keyCode.SPACE ) return;
    }        
    parent_el = $(this).closest('tr');

    if($(this).val() == '') return;
    $(this).find('#purposed_tags_<?php echo $row_line ; ?>').html('<img src="/images/loader.gif" />');
    $.ajax({
      type: "GET",
      url: "<?php echo url_for('gtu/purposeTag');?>" + '/value/'+ $(this).val(),
      success: function(html)
      {
        parent_el.find('#purposed_tags_<?php echo $row_line ; ?>').html(html);
        parent_el.find('#purposed_tags_<?php echo $row_line ; ?>').show();
      }
    });
  }
  
  function purposeTagsViaButton(event)
  {
	  $('input.tag_line_<?php echo $row_line ; ?>').click();
  }

  $('#purposed_tags_<?php echo $row_line ; ?> li').live('click',function()
  {
    input_el = $(this).closest('tr').find('input.tag_line_<?php echo $row_line ; ?>');
    if(input_el.val().match("\;\s*$"))
      input_el.val( input_el.val() + $(this).text() );
    else
      input_el.val( input_el.val() + " ; " +$(this).text() );
    input_el.trigger('click');
  });  
</script>
