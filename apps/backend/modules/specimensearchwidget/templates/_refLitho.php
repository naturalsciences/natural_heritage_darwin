<table>
  <thead>
    <tr>
      <td colspan="2">
        <input type="button" id='litho_precise' value="<?php echo __('Precise search'); ?>" disabled>
        <input type="button" id='litho_full_text' value="<?php echo __('Name search'); ?>">
      </td>
    </tr>
    <tr id="litho_full_text_line" class="hidden">
      <th><?php echo $form['litho_name']->renderLabel();?></th>
      <th><?php echo $form['litho_level_ref']->renderLabel(__('Level'));?></th>
    </tr>
  </thead>
  <tbody>
    <tr id="litho_full_text_line" class="hidden">
      <td><?php echo $form['litho_name'];?></td>
      <td><?php echo $form['litho_level_ref'];?></td>
    </tr>
    <tr id="litho_precise_line">
      <td id="litho_relation"><?php echo $form['litho_relation'];?></td>
      <td><?php echo $form['litho_item_ref'];?></td>
      <td>
        <ul id="litho_child_syn_included">
          <li><?php echo $form['litho_child_syn_included']->renderLabel();?></li>
          <li><?php echo $form['litho_child_syn_included'];?></li>
        </ul>
      </td>
    </tr>
  </tbody>
</table>

<script type="text/javascript">
$(document).ready(function () {
  $('#litho_precise').click(function() {
    $('#litho_precise').attr('disabled','disabled') ;
    $('#litho_full_text').removeAttr('disabled') ;
    $('#litho_precise_line').toggle() ;
    $(this).closest('table').find('#litho_full_text_line').toggle() ;
    $('#litho_full_text_line').find('input:text').val("") ;
    $('#litho_full_text_line').find('select').val('') ;
  });
  
  $('#litho_full_text').click(function() {
    $('#litho_precise').removeAttr('disabled') ;
    $('#litho_full_text').attr('disabled','disabled') ;
    $('#litho_precise_line').toggle() ;
    $(this).closest('table').find('#litho_full_text_line').toggle() ;
    $('#litho_precise_line').find('input:text').val("") ;
    $('#litho_precise_line').find('input:hidden').val('') ;

  }); 
  
  if($('#specimen_search_filters_litho_name').val() != '')
  {
    $('#litho_full_text').trigger("click") ;
  }

  $('#litho_relation ul.radio_list input').click(function () {
    if ( $(this).val() in { child : "child", direct_child : "direct_child" } ) {
      $('#litho_child_syn_included').removeClass('hidden');
    }
    else {
      $('#litho_child_syn_included').addClass('hidden');
    }
  });

  if (!($('#litho_relation input:checked').val() in { child : "child", direct_child : "direct_child" } )) {
    $('#litho_child_syn_included').addClass('hidden');
  }

});
</script>
