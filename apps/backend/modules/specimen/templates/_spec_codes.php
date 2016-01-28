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
  
    //2015 10 15 call mask handling
    try_mask();
	
    $("#clear_code_<?php echo $rownum;?>").click( function()
    {
      parent_el = $(this).closest('tbody');
      $(parent_el).find('input[type="text"]').val('');
      $(parent_el).find('select').append("<option value=''></option>").val('');   
      $(parent_el).hide();
      visibles = $(parent_el).closest('table.property_values').find('tbody:visible').size();
      if(!visibles)
      {
        $(this).closest('table.property_values').find('thead').hide();
      }
    });
  });
  
   //2015 10 15 define input mask handling for codes
   function try_mask()
   {
		$(".take_mask").val(<?php if(isset($codemask)){print("\"".$codemask."\"");} else{print("");} ?>);
		<?php if(sfContext::getInstance()->getActionName()=="addCode"):?>
			$(".mrac_input_mask").inputmask(<?php if(isset($codemask)){print("\"".$codemask."\"");}else{print("");} ?>);
		<?php endif;?>
		$(".class_rmca_mask_display").text('Mask: '+'<?php if(isset($codemask)){print("\"".$codemask."\"");}else{print("");} ?>');
   }
</script>
<?php endif;?>
