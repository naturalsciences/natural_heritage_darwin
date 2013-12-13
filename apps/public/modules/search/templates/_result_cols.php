<script type="text/javascript">

$(document).ready(function () {

  //Init custom checkbox
  $('input[type=checkbox], input[type=radio]').not('label.custom-label input').customRadioCheck();

  // Init
  $('#specimen_search_filters_col_fields').val(getSearchColumnVisibilty());
  
  $(".col_switcher :checkbox").change(function(){
    el = $('.col_' + $(this).val());
    if($(this).is(':checked'))
      el.show();
    else
      el.hide();
      
    //Update visible column list
    $('#specimen_search_filters_col_fields').val(getSearchColumnVisibilty());
  });

  $('.drawer_column :checkbox').change(function(){
    if($(this).is(':checked')) {
      $(this).closest('fieldset').attr('class','opened') ;
      $('table.drawer').removeClass('hidden');
    }
    else{
      $(this).parent().closest('fieldset').attr('class','closed') ;
      $('table.drawer').addClass('hidden');
    }
  });
});
</script>
<div id="fields">
<fieldset id="fld_spec" class="closed">
  <legend>
    <label class="drawer_column"><input type="checkbox" /> <?php echo __('Specimen criteria');?></label>
  </legend> 
  <table class="drawer hidden">
    <tr><td>
      <ul class="column_menu">
        <?php foreach($columns as $col_name => $col):?>
          <li>
            <label class="col_switcher">
              <input type="checkbox" value="<?php echo $col_name;?>" <?php if($field_to_show[$col_name]=='check') echo 'checked="checked"'; ?> />
              <?php echo $col[1];?>
            </label>
          </li>
        <?php endforeach;?>
     </ul>  
    </td></tr>
  </table>
</fieldset>
</div>

