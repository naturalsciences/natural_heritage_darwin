<tbody style="border: 1px solid #C1CF56;" class="spec_properties_data" id="spec_properties_data_<?php echo $rownum;?>">
	<tr>
		<td colspan="2" class="widget_row_delete" style="text-align:right;">
            <?php echo image_tag('remove.png', 'alt=Delete class=clear_property id=clear_property_'.$rownum); ?>
            <?php echo $form->renderHiddenFields() ?>
          </td>
	<tr>
    <tr>
      <th class="top_aligned"><?php echo $form['property_type']->renderLabel();?></th>
      <td>
        <?php echo $form['property_type']->renderError(); ?>
        <?php echo $form['property_type'];?>
      </td>
    </tr>
    <tr>
      <th class="top_aligned"><?php echo $form['applies_to']->renderLabel();?></th>
      <td>
        <?php echo $form['applies_to']->renderError(); ?>
        <?php echo $form['applies_to'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['date_from']->renderLabel();?></th>
      <td>
        <?php echo $form['date_from']->renderError(); ?>
        <?php echo $form['date_from'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['date_to']->renderLabel();?></th>
      <td>
        <?php echo $form['date_to']->renderError(); ?>
        <?php echo $form['date_to'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['method']->renderLabel();?></th>
      <td>
        <?php echo $form['method']->renderError(); ?>
        <?php echo $form['method'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['is_quantitative']->renderLabel();?></th>
      <td>
        <?php echo $form['is_quantitative']->renderError(); ?>
        <?php echo $form['is_quantitative'];?>
      </td>
    </tr>
    <tr>
      <th colspan="2" style="text-align:center">
        <label for="is_range"><?php echo __('Is range');?></label>
        <input type="checkbox" id="is_range_<?php echo $rownum;?>" name="is_range_<?php echo $rownum;?>" />
      </th>
    </tr>
    <tr class="prop_values">
      <th class="range_value range_value_<?php print($rownum);?>"><?php echo $form['lower_value']->renderLabel();?></th>
      <th class="single_value single_value_<?php print($rownum);?>"><?php echo $form['lower_value']->renderLabel('Value');?></th>
      <td>
        <?php echo $form['lower_value']->renderError(); ?>
        <?php echo $form['lower_value'];?>
      </td>
    </tr>
    <tr class="prop_values">
      <th class="range_value range_value_<?php print($rownum);?>"><?php echo $form['upper_value']->renderLabel();?></th>
      <td  class="range_value range_value_<?php print($rownum);?>">
        <?php echo $form['upper_value']->renderError(); ?>
        <?php echo $form['upper_value'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['property_unit']->renderLabel();?></th>
      <td>
        <?php echo $form['property_unit']->renderError(); ?>
        <?php echo $form['property_unit'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['property_accuracy']->renderLabel();?></th>
      <td>
        <?php echo $form['property_accuracy']->renderError(); ?>
        <?php echo $form['property_accuracy'];?>
      </td>
    </tr>

  </tbody>
  <tr>
  <td></td>
  </tr>
  <script  type="text/javascript">
$(document).ready(function () {

	  $("#clear_property_<?php echo $rownum;?>").click( function()
      {
       console.log("DELETE");
        parent_el = $(this).closest('tbody');
        parentTableId = $(parent_el).closest('table').attr('id');

        /*$(parent_el).find('textarea[id$=\"_category\"]').val(''); 
        
        $(parent_el).find('input[id$=\"_check\"]').remove(); 
		*/		
        $(parent_el).hide();
        visibles = $('table#'+parentTableId+' tbody.spec_properties_data:visible').size();
        if(!visibles)
        {
          $(this).closest('table#'+parentTableId).find('thead').hide();
        }
      });

  function toggleRangeValue(){
    if($(this).is(':checked')) {
      $('.range_value_<?php echo $rownum;?>').show();
      $('.single_value_<?php echo $rownum;?>').hide();
    } else {
      $('.range_value').hide();
      $('.single_value_<?php echo $rownum;?>').show();
    }
  }

  if($('#properties_upper_value_<?php echo $rownum;?>').val() != '') {
    $('#is_range_<?php echo $rownum;?>').attr('checked','checked');
  }
  $('#is_range_<?php echo $rownum;?>').change(toggleRangeValue);
  $('#is_range_<?php echo $rownum;?>').trigger('change');

  /*function addPropertyValue(event)
  {
    hideForRefresh('#property_screen');
    event.preventDefault();
    $.ajax(
    {
      type: "GET",
      url: $(this).attr('href')+ (0+$('.property_values tbody#property tr').length),
      success: function(html)
      {
        $('.property_values tbody#property').append(html);
        showAfterRefresh('#property_screen');
      }
    });
    return false;
  }*/

	var initSelectProp=function(param)
	{
		console.log("<?php echo url_for('property/getUnit');?>/type/"+param);
		  $.get("<?php echo url_for('property/getUnit');?>/type/"+param, function (data) {
		  $("#specimen_newProperties_<?pho print($rownum);?>_property_unit_parent select").html(data);
		  $("#specimen_newProperties_<?pho print($rownum);?>_property_accuracy_unit_parent select").html(data);
		  $("#specimen_newProperties_<?pho print($rownum);?>_property_qualifier_parent select").html(' ');
		  });

		  console.log("<?php echo url_for('property/getApplies');?>/type/"+param);
		  $.get("<?php echo url_for('property/getApplies');?>/type/"+param, function (data) {
			$("#properties_property_sub_type_parent select").html(data);
		  });
	}

    $('#specimen_newProperties_<?php print($rownum);?>_property_type').change(function() {
	  console.log("changed");
	  initSelectProp($(this).val());


    });

	initSelectProp();
  });
</script>