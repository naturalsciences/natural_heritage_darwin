<?php if($form['code_category']->getValue()!=""):?>
<tbody id="code_<?php echo $rownum;?>">
  <?php if($form->hasError()): ?>
  <tr>
    <td colspan="7">
      <?php echo $form->renderError();?>
    </td>
  </tr>
  <?php endif;?>
  <tr>
    <td>
      <?php echo $form['code_category'];?>
    </td>
    <td>
      <?php echo $form['code_prefix'];?>
    </td>
    <td>
      <?php echo $form['code_prefix_separator'];?>
    </td>
    <td>
      <?php echo $form['code'];?>
    </td>
    <td>
      <?php echo $form['code_suffix_separator'];?>
    </td>
    <td>
      <?php echo $form['code_suffix'];?>
    </td>
    <td class="widget_row_delete">
      <?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_code_'.$rownum); ?>
      <?php echo $form->renderHiddenFields();?>
    </td>    
  </tr>
</tbody>
<script type="text/javascript">
  $(document).ready(function () {

    $("#clear_code_<?php echo $rownum;?>").click( function() {
      parent_el = $(this).closest('tbody');
      parent_table = $(this).closest('table.property_values');
      if (typeof $(parent_table).data("initial_values") != "undefined") {
        var initial_code_object = $(parent_table).data("initial_values");
        if (typeof initial_code_object != "undefined" && ($(parent_el).find("input.code_mrac_input_mask").attr('id') in initial_code_object)) {
          delete initial_code_object[$(parent_el).find("input.code_mrac_input_mask").attr('id')];
          $(parent_table).data("initial_values", initial_code_object);
        }
      }
      $(parent_el).find('input[type="text"]').val('');
      $(parent_el).find('select').append("<option value=''></option>").val('');   
      $(parent_el).hide();
      visibles = $(parent_el).closest('table.property_values').find('tbody:visible').size();
      if(!visibles)
      {
        $(this).closest('table.property_values').find('thead').hide();
      }
    });

    $("tbody#code_<?php echo $rownum;?> input.code_mrac_input_mask").on(
      'change',
      function(){
        parent_table = $(this).closest('table.property_values');
        if (typeof $(parent_table).data("initial_values") != "undefined") {
          var initial_code_object = $(parent_table).data("initial_values");
          if (typeof initial_code_object != "undefined" && ($(this).attr('id') in initial_code_object)) {
            initial_code_object[$(this).attr('id')] = $(this).val();
            $(parent_table).data("initial_values", initial_code_object);
          }
        }
      }
    );

  });
  
</script>
<?php endif;?>
