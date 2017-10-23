<tr class="tag_line">
  <td>
    <?php echo $form['tag'];?>
    <div class="purposed_tags" id="purposed_tags_<?php echo $row_line;?>">
    </div>
  </td>
  <td>
    <?php echo image_tag('remove.png', 'alt=Delete class=clear_prop id=clear_tag_'.$row_line); ?>
  </td>
</tr>
<script  type="text/javascript">
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
      url: "<?php echo url_for('search/purposeTag');?>" + '/value/'+ $(this).val(),
      success: function(html)
      {
        parent_el.find('#purposed_tags_<?php echo $row_line ; ?>').html(html);
        parent_el.find('#purposed_tags_<?php echo $row_line ; ?>').show();
      }
    });
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
