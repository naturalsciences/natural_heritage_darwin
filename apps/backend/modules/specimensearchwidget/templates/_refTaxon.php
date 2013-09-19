<table>
  <thead>
    <tr>
      <td width='175px'>
        <input type="button" id='taxon_precise' value="<?php echo __('Precise'); ?>" disabled>
        <input type="button" id='taxon_full_text' value="<?php echo __('Full text'); ?>">
      </td>
      <td width='300px'>&nbsp;</td>
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
      <td><?php echo $form['taxon_relation'];?></td>
      <td><?php echo $form['taxon_item_ref'];?></td>
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
    $('#taxon_full_text_line').find('input:text').val("") ;
    $('#taxon_full_text_line').find('select').val('') ;
  });
  
  $('#taxon_full_text').click(function() {
    $('#taxon_precise').removeAttr('disabled') ;
    $('#taxon_full_text').attr('disabled','disabled') ;
    $('#taxon_precise_line').toggle() ;
    $(this).closest('table').find('#taxon_full_text_line').toggle() ;
    $('#taxon_full_text_line').find('input:text').val("") ;
    $('#taxon_full_text_line').find('input:hidden').val('') ;

  }); 
  
  if($('#specimen_search_filters_taxon_name').val() != '')
  {
    $('#taxon_full_text').trigger("click") ;
  }
});
</script>