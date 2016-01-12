<?php if($form['relationship_type']->getValue()!=""):?>
<tbody id="specimens_accompanying_<?php echo $rownum;?>">
  <?php if($form->hasError()): ?>
  <tr>
    <td colspan="5">
      <?php echo $form->renderError();?>
    </td>
  </tr>
  <?php endif;?>
  <tr>
    <td class="unit_type">
      <?php echo $form['unit_type']->renderLabel();?> <?php echo $form['unit_type'];?>
    </td>
    <td colspan="2"  style="text-align:right;">
      <?php echo $form['relationship_type']->renderLabel();?><?php echo $form['relationship_type'];?>
    </td>
    <td></td>
  </tr>
  <tr class="relationship_detail_edit">
    <td class="unit_choose" colspan="3">
      <div class="unit_taxonomy"><?php echo $form['taxon_ref'];?></div>
      <div class="unit_mineral"><?php echo $form['mineral_ref'];?>
        <?php echo $form['quantity']->render(array('placeholder'=>$form['quantity']->renderLabelName()));;?>
        <?php echo $form['unit'];?>
      </div>
      <div class="unit_specimens"><?php echo $form['specimen_related_ref'];?></div>
      <div class="unit_external">
        <?php echo $form['institution_ref']->renderLabel();?><?php echo $form['institution_ref'];?>
        <?php echo $form['source_name']->render(array('placeholder'=>$form['source_name']->renderLabelName()));?>
        <?php echo $form['source_id']->render(array('placeholder'=>$form['source_id']->renderLabelName()));?>
      </div>
    </td>
    <td class="widget_row_delete">
      <?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_accompanying_unit_'.$rownum); ?>
      <?php echo $form->renderHiddenFields();?>
    </td>
   </tr>
</tbody>
<script type="text/javascript">
  $(document).ready(function () {
    $("#clear_accompanying_unit_<?php echo $rownum;?>").click( function()
    {
      parent_el = $(this).closest('tbody');
      $(parent_el).find('input').val('');
      $(parent_el).find('select').append("<option value=''></option>").val('');
      $(parent_el).hide();
      visibles = $(parent_el).closest('table.property_values').find('tbody:visible').size();
      if(!visibles)
      {
        $(this).closest('table.property_values').find('thead').hide();
      }
    });

    $('#specimens_accompanying_<?php echo $rownum;?> .unit_type select').change(function()
    {
      type = $(this).val();
      top_row = $('#specimens_accompanying_<?php echo $rownum;?>');
      top_row.find('.unit_choose > div').hide();
      top_row.find('.unit_' + type).show();

      top_row.find('.extd_info').hide();
      top_row.find('.extd_info_'+ type).show();

    });

     $('#specimens_accompanying_<?php echo $rownum;?> .unit_type select').change();
  });
</script>
<?php endif;?>
