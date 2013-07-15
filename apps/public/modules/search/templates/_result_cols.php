<script type="text/javascript">

$(document).ready(function () {
  /*$('#fld_spec legend').click(function()
  
   /* if( $(this).parent().attr('class') == 'closed')
    {
      $(this).parent().attr('class','opened') ;
      $(this).parent().find('table').show();
      $(this).find('.collapsed').show();
      $(this).find('.expanded').hide();
    }
    else
    {
      $(this).parent().attr('class','closed') ;
      $(this).parent().find('table').hide();
      $(this).find('.collapsed').hide();
      $(this).find('.expanded').show();
    }
  });
  $('#fld_spec').attr('class','closed') ;
  $('#fld_spec').find('table').hide();
  $('#fld_spec').find('.collapsed').hide();
  $('#fld_spec').find('.expanded').show();
*/
    
  //Init custom checkbox
  $('input[type=checkbox], input[type=radio]').customRadioCheck();

  
  /**** init COL MANAGEMENT ***/
  $('ul.column_menu .col_switcher :not(:checked)').each(function(){
    $('.col_' + $(this).val()).hide();
  });

  $(".col_switcher :checkbox").change(function(){
    el = $('.col_' + $(this).val());
    if($(this).is(':checked'))
      el.show();
    else
      el.hide();
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

