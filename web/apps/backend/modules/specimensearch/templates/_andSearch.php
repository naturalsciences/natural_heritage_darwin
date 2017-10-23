<tr class="tag_line">
  <td colspan="2">
    <?php echo $form['tag'];?>

    <div class="purposed_tags" id="purposed_tags_<?php echo $row_line;?>">
    </div>
  </td>
  <td>
  	 <b><?php echo $form['fuzzy_matching_tag']->renderLabel();?></b>:<?php echo $form['fuzzy_matching_tag'];?>
  </td>
   <td>
	<input type="button" value="Tag cloud" name="btn_fuz_<?php echo($row_line);?>" id="btn_fuz_<?php echo($row_line);?>" class="result_choose"/>
  </td>
  
   <td class="widget_row_delete"  style="text-align:right">
    <b>Delete:</b><?php echo image_tag('remove.png', 'alt=Delete class=clear_prop id=clear_tag_'.$row_line); ?>
  </td>
</tr>
<script  type="text/javascript">
  $('#btn_fuz_<?php echo $row_line ; ?>').bind('click',purposeTagsViaButton);
  $('input.tag_line_<?php echo $row_line ; ?>').bind('keydown click',purposeTags);
  $('#clear_tag_<?php echo $row_line;?>').click(function(){
    if($(this).closest('tbody').find('tr.tag_line').length == 1)
    {
      $(this).closest('tr').find('td input').val('');
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
	//ftheeten 2016 01 16
	
    /*if(input_el.val().match("\;\s*$"))
      input_el.val( input_el.val() + $(this).text() );
    else
      input_el.val( input_el.val() + " ; " +$(this).text() );*/
	  input_el.val($(this).text());
    input_el.trigger('click');
  });  
</script>
