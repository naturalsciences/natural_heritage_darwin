<table>
  <tbody>
    <tr>
        <td colspan="2">
          <?php echo $form->renderGlobalErrors() ?>
        </td>
    </tr>
    <tr>
      <th class="top_aligned"><?php echo $form['MassActionForm']['add_property']['property_type']->renderLabel();?></th>
      <td>
        <?php echo $form['MassActionForm']['add_property']['property_type']->renderError(); ?>
        <?php echo $form['MassActionForm']['add_property']['property_type'];?>
      </td>
    </tr>
    <tr>
      <th class="top_aligned"><?php echo $form['MassActionForm']['add_property']['applies_to']->renderLabel();?></th>
      <td>
        <?php echo $form['MassActionForm']['add_property']['applies_to']->renderError(); ?>
        <?php echo $form['MassActionForm']['add_property']['applies_to'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['MassActionForm']['add_property']['date_from']->renderLabel();?></th>
      <td>
        <?php echo $form['MassActionForm']['add_property']['date_from']->renderError(); ?>
        <?php echo $form['MassActionForm']['add_property']['date_from'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['MassActionForm']['add_property']['date_to']->renderLabel();?></th>
      <td>
        <?php echo $form['MassActionForm']['add_property']['date_to']->renderError(); ?>
        <?php echo $form['MassActionForm']['add_property']['date_to'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['MassActionForm']['add_property']['method']->renderLabel();?></th>
      <td>
        <?php echo $form['MassActionForm']['add_property']['method']->renderError(); ?>
        <?php echo $form['MassActionForm']['add_property']['method'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['MassActionForm']['add_property']['is_quantitative']->renderLabel();?></th>
      <td>
        <?php echo $form['MassActionForm']['add_property']['is_quantitative']->renderError(); ?>
        <?php echo $form['MassActionForm']['add_property']['is_quantitative'];?>
      </td>
    </tr>
    <tr>
      <th colspan="2" style="text-align:center">
        <label for="is_range"><?php echo __('Is range');?></label>
        <input type="checkbox" id="is_range" name="is_range" />
      </th>
    </tr>
    <tr class="prop_values">
      <th class="range_value"><?php echo $form['MassActionForm']['add_property']['lower_value']->renderLabel();?></th>
      <th class="single_value"><?php echo $form['MassActionForm']['add_property']['lower_value']->renderLabel('Value');?></th>
      <td>
        <?php echo $form['MassActionForm']['add_property']['lower_value']->renderError(); ?>
        <?php echo $form['MassActionForm']['add_property']['lower_value'];?>
      </td>
    </tr>
    <tr class="prop_values">
      <th class="range_value"><?php echo $form['MassActionForm']['add_property']['upper_value']->renderLabel();?></th>
      <td  class="range_value">
        <?php echo $form['MassActionForm']['add_property']['upper_value']->renderError(); ?>
        <?php echo $form['MassActionForm']['add_property']['upper_value'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['MassActionForm']['add_property']['property_unit']->renderLabel();?></th>
      <td>
        <?php echo $form['MassActionForm']['add_property']['property_unit']->renderError(); ?>
        <?php echo $form['MassActionForm']['add_property']['property_unit'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['MassActionForm']['add_property']['property_accuracy']->renderLabel();?></th>
      <td>
        <?php echo $form['MassActionForm']['add_property']['property_accuracy']->renderError(); ?>
        <?php echo $form['MassActionForm']['add_property']['property_accuracy'];?>
      </td>
    </tr>
  <tr>
  <tr>
  </tbody>
</table>





<script  type="text/javascript">
$(document).ready(function () {
  $('form.qtiped_form').modal_screen();
  
  function toggleRangeValue(){
    if($(this).is(':checked')) {
      $('.range_value').show();
      $('.single_value').hide();
    } else {
      $('.range_value').hide();
      $('.single_value').show();
    }
  }

  if($('#properties_upper_value').val() != '') {
    $('#is_range').attr('checked','checked');
  }
  $('#is_range').change(toggleRangeValue);
  $('#is_range').trigger('change');

  function addPropertyValue(event)
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
  }

    $('#properties_property_type').change(function() {
      $.get("<?php echo url_for('property/getUnit');?>/type/"+$(this).val(), function (data) {
      $("#properties_property_unit_parent select").html(data);
      $("#properties_property_accuracy_unit_parent select").html(data);
      $("#properties_property_qualifier_parent select").html(' ');
      });

      $.get("<?php echo url_for('property/getApplies');?>/type/"+$(this).val(), function (data) {
        $("#properties_property_sub_type_parent select").html(data);
      });

    });
    changeSubmit(true);
  });
</script>
</div>