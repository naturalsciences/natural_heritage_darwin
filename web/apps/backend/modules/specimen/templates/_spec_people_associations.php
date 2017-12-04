          <tr>
              <td colspan="3">
                <?php echo $form->renderError();?>
              </td>
          </tr>
          <tr class="spec_ident_<?php echo $type;?>_data" id="<?php echo $type;?>_<?php echo $row_num; ?>">
            <td><?php echo image_tag('drag.png','class=spec_ident_'.$type.'_handle');?></td>
            <td><?php echo $form['people_ref']->renderLabel();?></td>
            <td class="widget_row_delete">
              <?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_'.$type.'_'.$row_num); ?>
              <?php echo $form->renderHiddenFields();?>
    <script type="text/javascript">
      $(document).ready(function () {
        $("#clear_<?php echo $type;?>_<?php echo $row_num;?>").click( function()
        {
           parent_el = $(this).closest('tr');
           parentTableId = $(parent_el).closest('table').attr('id')
           $(parent_el).find('input[id$=\"_people_ref\"]').val('');
           $(parent_el).hide();
           $.fn.catalogue_people.reorder( $(parent_el).closest('table') );
           visibles = $('table#'+parentTableId+' .spec_ident_<?php echo $type;?>_data:visible').size();
           if(!visibles)
           {
            $(this).closest('table#'+parentTableId).find('thead').hide();
           }
        });
        $('table .hidden_record').each(function() {
          $(this).closest('tr').hide() ;
        });
      });
    </script>
            </td>
          </tr>
