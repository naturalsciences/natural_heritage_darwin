          <?php if($form->hasError()):?><tr>
              <td colspan="3">
                <?php echo $form->renderError();?>
              </td>
          </tr>
          <?php endif;?>
          <tr class="spec_ident_collectors_data" id="collector_<?php echo $row_num; ?>">
            <td class="spec_ident_collectors_handle"><?php echo image_tag('drag.png');?></td>
            <td><?php echo $form['people_ref']->renderLabel();?></td>
            <td class="widget_row_delete">
              <?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_collector_'.$row_num); ?>
              <?php echo $form->renderHiddenFields();?>
              <script type="text/javascript">
                $(document).ready(function () {
                  $("#clear_collector_<?php echo $row_num;?>").click( function()
                  {
                     parent_el = $(this).closest('tr');
                     parent_el.find('input[id$=\"_people_ref\"]').val('');
                     parent_el.hide();
                     $.fn.catalogue_people.reorder(parent_el.closest('table'));
                  });
                });
              </script>
            </td>
          </tr>
