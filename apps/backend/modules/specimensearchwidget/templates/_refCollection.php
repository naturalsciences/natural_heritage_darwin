<script type="text/javascript">
$(document).ready(function () {
    $('.treelist li:not(li:has(ul)) img.tree_cmd').hide();
    $('.collapsed').click(function()
    {
        $(this).hide();
        $(this).siblings('.expanded').show();
        $(this).parent().siblings('ul').show();
    });
    
    $('.expanded').click(function()
    {
        $(this).hide();
        $(this).siblings('.collapsed').show();
        $(this).parent().siblings('ul').hide();
    });
    $('.treelist li input[type=checkbox]').click(function()
    {
	    class_val = $(this).closest('li').attr('class');
   	  val = $(this).attr('checked') ;
	    alt_val = $(this).closest('ul .'+class_val).find(':checkbox').attr('checked',val);
    });    
    $('#clear_collections').click(function()
    {
  	  $('table.widget_sub_table').find(':checkbox').removeAttr('checked');
    });
  
    $('#check_editable').click(function(){
      $('.treelist input:checkbox').removeAttr('checked');
      $('li[data-enc] > div > input:checkbox').attr('checked','checked');
    });
});
</script>
<table class="widget_sub_table">
  <tr>
    <td>
      <div class="treelist">
		    <?php echo $form['collection_ref'] ; ?>
      </div>
      <div class="check_right">
      <?php if($sf_user->isAtLeast(Users::ENCODER)):?>
         <input type="button" class="result_choose" value="<?php echo __('check only editable');?>" id="check_editable">
      <?php endif;?>
        <input type="button" class="result_choose" value="<?php echo __('clear');?>" id="clear_collections">
      </div>

	  </td>
	</tr>
</table>

