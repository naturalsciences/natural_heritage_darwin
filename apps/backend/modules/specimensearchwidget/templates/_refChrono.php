<table>
  <thead>
    <tr>
      <td colspan="2">
        <input type="button" id='chrono_precise' value="<?php echo __('Precise search'); ?>" disabled>
        <input type="button" id='chrono_full_text' value="<?php echo __('Name search'); ?>">
      </td>
    </tr>
    <tr id="chrono_full_text_line" class="hidden">
      <th><?php echo $form['chrono_name']->renderLabel();?></th>
      <th><?php echo $form['chrono_level_ref']->renderLabel(__('Level'));?></th>
    </tr>
  </thead>
  <tbody>
    <tr id="chrono_full_text_line" class="hidden">
      <td><?php echo $form['chrono_name'];?></td>
      <td><?php echo $form['chrono_level_ref'];?></td>
    </tr>
    <tr id="chrono_precise_line">
      <td id="chrono_relation"><?php echo $form['chrono_relation'];?></td>
      <td><?php echo $form['chrono_item_ref'];?></td>
      <td>
        <ul id="chrono_child_syn_included">
          <li><?php echo $form['chrono_child_syn_included']->renderLabel();?></li>
          <li><?php echo $form['chrono_child_syn_included'];?></li>
        </ul>
      </td>
    </tr>
  </tbody>
</table>

<script type="text/javascript">
$(document).ready(function () {
  $('#chrono_precise').click(function() {
    $('#chrono_precise').attr('disabled','disabled') ;
    $('#chrono_full_text').removeAttr('disabled') ;
    $('#chrono_precise_line').toggle() ;
    $(this).closest('table').find('#chrono_full_text_line').toggle() ;
    $('#chrono_full_text_line').find('input:text').val("") ;
    $('#chrono_full_text_line').find('select').val('') ;
  });
  
  $('#chrono_full_text').click(function() {
    $('#chrono_precise').removeAttr('disabled') ;
    $('#chrono_full_text').attr('disabled','disabled') ;
    $('#chrono_precise_line').toggle() ;
    $(this).closest('table').find('#chrono_full_text_line').toggle() ;
    $('#chrono_precise_line').find('input:text').val("") ;
    $('#chrono_precise_line').find('input:hidden').val('') ;

  }); 
  
  if($('#specimen_search_filters_chrono_name').val() != '')
  {
    $('#chrono_full_text').trigger("click") ;
  }

  $('#chrono_relation ul.radio_list input').click(function () {
    if ( $(this).val() in { child : "child", direct_child : "direct_child" } ) {
      $('#chrono_child_syn_included').removeClass('hidden');
    }
    else {
      $('#chrono_child_syn_included').addClass('hidden');
    }
  });

  if (!($('#chrono_relation input:checked').val() in { child : "child", direct_child : "direct_child" } )) {
    $('#chrono_child_syn_included').addClass('hidden');
  }

});
</script>
