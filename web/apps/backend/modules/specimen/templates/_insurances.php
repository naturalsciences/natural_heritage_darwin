<tbody  class="parts_insurances_data" id="parts_insurances_data_<?php echo $rownum;?>">
  <?php if($form->hasError()): ?>
  <tr>
    <td colspan="5">
      <?php echo $form->renderError();?>
    </td>
  </tr>
  <?php endif;?>
  <tr>
    <td>
      <?php echo $form['date_from'];?>
    </td>
    <td>
      <?php echo $form['date_to'];?>
    </td>    
    <td>
      <?php echo $form['insurance_value'];?>
    </td>    
    <td>
      <?php echo $form['insurance_currency'];?>
    </td>
    <td class="widget_row_delete" rowspan="2">
      <?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_insurance_'.$rownum); ?>
      <?php echo $form->renderHiddenFields();?>
    </td>    
  </tr>
  <tr>
    <td class="left_tabed">
      <?php echo $form['insurer_ref']->renderLabel();?>
    </td>
    <td colspan="4">
      <?php echo $form['insurer_ref'];?>
    </td>
  </tr>
  <tr>
    <td class="left_tabed">
      <?php echo $form['contact_ref']->renderLabel();?>:
    </td>
    <td colspan="4">
      <?php echo $form['contact_ref'];?>
    </td>
  </tr>  
</tbody>
<script type="text/javascript">
  $(document).ready(function () {
    $("#clear_insurance_<?php echo $rownum;?>").click( function()
    {
      parent_el = $(this).closest('tbody');
      parent_tr = $(this).closest('tr');
      $(parent_el).find('input[id$=\"_insurance_value\"]').val('');
      $(parent_el).find('input[id$=\"_insurance_currency_input\"]').val('');
      $(parent_el).find('input[id$=\"_referenced_relation\"]').val('');
      $(parent_el).find('select').append("<option value=''></option>").val('');
      $(parent_el).hide();
      $(parent_tr).next('tr').hide();
      $(parent_tr).next('tr').next('tr').hide();      
      visibles = $(parent_el).find('tr:visible').size();
      if(!visibles)
      {
        $(this).closest('table.property_values').find('thead').hide();
      }
    });
  });
</script>
