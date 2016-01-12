<script type="text/javascript">
$(document).ready(function () {
    $('.treelist li:not(li:has(ul)) img.tree_cmd').hide();
    $('.chk input').change(function()
    {
      li = $(this).closest('li');
      if(! $(this).is(':checked'))
        li.find(':checkbox').not($(this)).removeAttr('checked').change();
      else
        li.find(':checkbox').not($(this)).attr('checked','checked').change();
    });

    $('#clear_collections').click(function()
    {
       $('table.widget_sub_table').find(':checked').removeAttr('checked').change();
    });

    $('.collapsed').click(function()
    {
        $(this).addClass('hidden');
        $(this).siblings('.expanded').removeClass('hidden');
        $(this).parent().siblings('ul').show();
    });

    $('.expanded').click(function()
    {
        $(this).addClass('hidden');
        $(this).siblings('.collapsed').removeClass('hidden');
        $(this).parent().siblings('ul').hide();
    });

    $('#check_editable').click(function(){
      $('.treelist input:checked').removeAttr('checked').change();
      $('li[data-enc] > div > label > input:checkbox').attr('checked','checked').change();
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
