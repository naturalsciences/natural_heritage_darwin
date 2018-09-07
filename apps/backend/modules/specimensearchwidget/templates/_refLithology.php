<table>
  <thead>
    <tr>
      <td colspan="2">
        <input type="button" id='lithology_precise' value="<?php echo __('Precise search'); ?>" disabled>
        <input type="button" id='lithology_full_text' value="<?php echo __('Name search'); ?>">
      </td>
    </tr>
    <tr id="lithology_full_text_line" class="hidden">
      <th><?php echo $form['lithology_name']->renderLabel();?></th>
      <th><?php echo $form['lithology_level_ref']->renderLabel(__('Level'));?></th>
    </tr>
  </thead>
  <tbody>
    <tr id="lithology_full_text_line" class="hidden">
      <td><?php echo $form['lithology_name'];?></td>
      <td><?php echo $form['lithology_level_ref'];?></td>
    </tr>
    <tr id="lithology_precise_line">
      <td id="lithology_relation"><?php echo $form['lithology_relation'];?></td>
      <td><?php echo $form['lithology_item_ref'];?></td>
      <td>
        <ul id="lithology_child_syn_included">
          <li><?php echo $form['lithology_child_syn_included']->renderLabel();?></li>
          <li><?php echo $form['lithology_child_syn_included'];?></li>
        </ul>
      </td>
    </tr>
  </tbody>
</table>

<script type="text/javascript">
$(document).ready(function () {
  $('#lithology_precise').click(function() {
    $('#lithology_precise').attr('disabled','disabled') ;
    $('#lithology_full_text').removeAttr('disabled') ;
    $('#lithology_precise_line').toggle() ;
    $(this).closest('table').find('#lithology_full_text_line').toggle() ;
  });
  
  $('#lithology_full_text').click(function() {
    $('#lithology_precise').removeAttr('disabled') ;
    $('#lithology_full_text').attr('disabled','disabled') ;
    $('#lithology_precise_line').toggle() ;
    $(this).closest('table').find('#lithology_full_text_line').toggle() ;

  }); 
  
  if($('#specimen_search_filters_lithology_name').val() != '')
  {
    $('#lithology_full_text').trigger("click") ;
  }

  $('#lithology_relation ul.radio_list input').click(function () {
    if ( $(this).val() in { child : "child", direct_child : "direct_child" } ) {
      $('#lithology_child_syn_included').removeClass('hidden');
    }
    else {
      $('#lithology_child_syn_included').addClass('hidden');
    }
  });

  if (!($('#lithology_relation input:checked').val() in { child : "child", direct_child : "direct_child" } )) {
    $('#lithology_child_syn_included').addClass('hidden');
  }

  $('.lithology_name').on(
    'change',
    function() {
      if($(this).val() !== '') {
        $('.lithology_autocomplete').val('');
      }
    }
  );

});
</script>
