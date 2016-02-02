<table>
  <thead>
    <tr>
      <td>
        <input type="button" id='mineral_precise' value="<?php echo __('Precise search'); ?>" disabled>
        <input type="button" id='mineral_full_text' value="<?php echo __('Name search'); ?>">
      </td>
      <td width='300px'>&nbsp;</td>
    </tr>
    <tr id="mineral_full_text_line" class="hidden">
      <th><?php echo $form['mineral_name']->renderLabel();?></th>
      <th><?php echo $form['mineral_level_ref']->renderLabel(__('Level'));?></th>
    </tr>
  </thead>
  <tbody>
    <tr id="mineral_full_text_line" class="hidden">
      <td><?php echo $form['mineral_name'];?></td>
      <td><?php echo $form['mineral_level_ref'];?></td>
    </tr>
    <tr id="mineral_precise_line">
      <td id="mineral_relation"><?php echo $form['mineral_relation'];?></td>
      <td><?php echo $form['mineral_item_ref'];?></td>
      <td>
        <ul id="mineral_child_syn_included">
          <li><?php echo $form['mineral_child_syn_included']->renderLabel();?></li>
          <li><?php echo $form['mineral_child_syn_included'];?></li>
        </ul>
      </td>
    </tr>
  </tbody>
</table>

<script type="text/javascript">
$(document).ready(function () {
  $('#mineral_precise').click(function() {
    $('#mineral_precise').attr('disabled','disabled') ;
    $('#mineral_full_text').removeAttr('disabled') ;
    $('#mineral_precise_line').toggle() ;
    $(this).closest('table').find('#mineral_full_text_line').toggle() ;
    $('#mineral_full_text_line').find('input:text').val("") ;
    $('#mineral_full_text_line').find('select').val('') ;
  });
  
  $('#mineral_full_text').click(function() {
    $('#mineral_precise').removeAttr('disabled') ;
    $('#mineral_full_text').attr('disabled','disabled') ;
    $('#mineral_precise_line').toggle() ;
    $(this).closest('table').find('#mineral_full_text_line').toggle() ;
    $('#mineral_precise_line').find('input:text').val("") ;
    $('#mineral_precise_line').find('input:hidden').val('') ;

  });
  
  if($('#specimen_search_filters_mineral_name').val() != '')
  {
    $('#mineral_full_text').trigger("click") ;
  }

  $('#mineral_relation ul.radio_list input').click(function () {
    if ( $(this).val() in { child : "child", direct_child : "direct_child" } ) {
      $('#mineral_child_syn_included').removeClass('hidden');
    }
    else {
      $('#mineral_child_syn_included').addClass('hidden');
    }
  });

  if (!($('#mineral_relation input:checked').val() in { child : "child", direct_child : "direct_child" } )) {
    $('#mineral_child_syn_included').addClass('hidden');
  }

});
</script>
