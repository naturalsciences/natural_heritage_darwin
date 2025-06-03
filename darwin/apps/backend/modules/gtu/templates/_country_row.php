
          <?php if($form->hasError()):?><tr>
              <td colspan="3">
                <?php echo $form->renderError();?>
              </td>
          </tr>
          <?php endif;?>
          <tr class="gtu_country_data" id="collector_<?php echo $row_num; ?>">
            <th><?php print(__('Country (ISO 3166)')." n°".($row_num +1));?> </th>
			 <td><?php echo $form['gtu_ref'];?><?php echo $form['country_ref'];?></td>
            <td class="widget_row_delete">
              <?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_country_'.$row_num); ?>
              <?php echo $form->renderHiddenFields();?>
              <script type="text/javascript">
                $(document).ready(function () {
                  $("#clear_country_<?php echo $row_num;?>").click( function()
                  {
                     parent_el = $(this).closest('tr');
                     //parent_el.find('#gtu_GtuToCountry_<?php echo $row_num;?>_country_ref').val('');
					 parent_el.html("");
                     parent_el.hide();
					 if($.fn.catalogue_people!==undefined)
					 {
						$.fn.catalogue_people.reorder(parent_el.closest('table'));
					 }
				  });
                });
              </script>
            </td>
          </tr>
