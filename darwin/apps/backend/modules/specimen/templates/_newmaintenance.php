<tbody style="border: 1px solid #C1CF56;" class="maintenance_data" id="maintenance_data_<?php echo $rownum;?>">
	<tr>
		<td colspan="2" class="widget_row_delete" style="text-align:right;">
            <?php echo image_tag('remove.png', 'alt=Delete class=clear_maintenance id=clear_maintenance_'.$rownum); ?>
            <?php echo $form->renderHiddenFields() ?>
          </td>
	<tr>
	<tr>
            <th><?php echo $form['category']->renderLabel();?></th>
            <td>
              <?php echo $form['category']->renderError() ?>
              <?php echo $form['category'];?>
            </td>
      </tr>
      <tr>
            <th><?php echo $form['action_observation']->renderLabel();?></th>
            <td>
              <?php echo $form['action_observation']->renderError() ?>
              <?php echo $form['action_observation'];?>
            </td>
      </tr>
      <tr>
            <th><?php echo $form['modification_date_time']->renderLabel();?></th>
            <td>
              <?php echo $form['modification_date_time']->renderError() ?>
              <?php echo $form['modification_date_time'];?>
            </td>
      </tr>
      <tr>
            <th><?php echo $form['people_ref']->renderLabel();?></th>
            <td>
              <?php echo $form['people_ref']->renderError() ?>
              <?php echo $form['people_ref'];?>
            </td>
      </tr>
      <tr>
            <th><?php echo $form['description']->renderLabel();?></th>
            <td>
              <?php echo $form['description']->renderError() ?>
              <?php echo $form['description'];?>
            </td>
      </tr>
</tbody>
<tr>
  <td></td>
 </tr>
  <script  type="text/javascript">
$(document).ready(function () {

	  $("#clear_maintenance_<?php echo $rownum;?>").click( function()
      {
       console.log("DELETE");
        parent_el = $(this).closest('tbody');
        parentTableId = $(parent_el).closest('table').attr('id');

        /*$(parent_el).find('textarea[id$=\"_category\"]').val(''); 
        
        $(parent_el).find('input[id$=\"_check\"]').remove(); 
		*/		
        $(parent_el).hide();
        visibles = $('table#'+parentTableId+' tbody.maintenance_data:visible').size();
        if(!visibles)
        {
          $(this).closest('table#'+parentTableId).find('thead').hide();
        }
      });


  });
</script> 