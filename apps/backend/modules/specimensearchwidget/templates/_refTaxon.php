<table>
  <thead>
    <tr>
      <td colspan="2">
        <input type="button" id='taxon_precise' value="<?php echo __('Precise search'); ?>" disabled>
        <input type="button" id='taxon_full_text' value="<?php echo __('Name search'); ?>">
      </td>
    </tr>
    <tr id="taxon_full_text_line" class="hidden">
      <th><?php echo $form['taxon_name']->renderLabel();?></th>
      <th><?php echo $form['taxon_level_ref']->renderLabel();?></th>
    </tr>
  </thead>
  <tbody>
    <tr id="taxon_full_text_line" class="hidden">
      <td><?php echo $form['taxon_name'];?></td>
      <td><?php echo $form['taxon_level_ref'];?></td>
    </tr>
    <tr id="taxon_precise_line">
      <td id="taxon_relation"><?php echo $form['taxon_relation'];?></td>
      <td><?php echo $form['taxon_item_ref'];?></td>
      <td>
        <ul id="taxon_child_syn_included">
          <li><?php echo $form['taxon_child_syn_included']->renderLabel();?></li>
          <li><?php echo $form['taxon_child_syn_included'];?></li>
        </ul>
      </td>
    </tr>
  </tbody>
</table>

<script type="text/javascript">
$(document).ready(function () {
  $('#taxon_precise').click(function() {
    $('#taxon_precise').attr('disabled','disabled') ;
    $('#taxon_full_text').removeAttr('disabled') ;
    $('#taxon_precise_line').toggle() ;
    $(this).closest('table').find('#taxon_full_text_line').toggle() ;
  });

  $('#taxon_full_text').click(function() {
    $('#taxon_precise').removeAttr('disabled') ;
    $('#taxon_full_text').attr('disabled','disabled') ;
    $('#taxon_precise_line').toggle() ;
    $(this).closest('table').find('#taxon_full_text_line').toggle() ;

  });

  if($('#specimen_search_filters_taxon_name').val() != '')
  {
    $('#taxon_full_text').trigger("click") ;
  }

  $('#taxon_relation ul.radio_list input').click(function () {
    if ( $(this).val() in { child : "child", direct_child : "direct_child" } ) {
      $('#taxon_child_syn_included').removeClass('hidden');
    }
    else {
      $('#taxon_child_syn_included').addClass('hidden');
    }
  });

  if (!($('#taxon_relation input:checked').val() in { child : "child", direct_child : "direct_child" } )) {
    $('#taxon_child_syn_included').addClass('hidden');
  }

  $('.taxon_name').on(
    'change',
    function() {
      if($(this).val() !== '') {
        $('.taxon_autocomplete').val('');
      }
    }
  );

});
</script>
