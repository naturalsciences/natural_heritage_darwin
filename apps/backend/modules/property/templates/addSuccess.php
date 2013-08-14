<?php include_javascripts_for_form($form) ?>
<div id="property_screen">
<?php echo form_tag('property/add?table='.$sf_params->get('table').'&id='.$sf_params->get('id').($form->getObject()->isNew() ? '': '&rid='.$form->getObject()->getId() ), array('class'=>'edition qtiped_form', 'id'=>'property_form'));?>

<?php echo $form['referenced_relation'];?>
<?php echo $form['record_id'];?>
<table>
  <tbody>
    <tr>
        <td colspan="2">
          <?php echo $form->renderGlobalErrors() ?>
        </td>
    </tr>
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
        <input type="checkbox" id="is_range" name="is_range" />
      </th>
    </tr>
    <tr class="prop_values">
      <th class="range_value"><?php echo $form['lower_value']->renderLabel();?></th>
      <th class="single_value"><?php echo $form['lower_value']->renderLabel('Value');?></th>
      <td>
        <?php echo $form['lower_value']->renderError(); ?>
        <?php echo $form['lower_value'];?>
      </td>
    </tr>
    <tr class="prop_values">
      <th class="range_value"><?php echo $form['upper_value']->renderLabel();?></th>
      <td  class="range_value">
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
  <tr>
  <tr>
  </tbody>
</table>


<table class="bottom_actions">
  <tfoot>
    <tr>
      <td>
        <a href="#" class="cancel_qtip"><?php echo __('Cancel');?></a>
        <?php if(! $form->getObject()->isNew()):?>
	  <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=properties&id='.$form->getObject()->getId());?>" title="<?php echo __('Are you sure ?') ?>">
	    <?php echo __('Delete');?>
	  </a>
        <?php endif;?> 
        <input id="submit" type="submit" value="<?php echo __('Save');?>" />
      </td>
    </tr>
  </tfoot>
</table>
</form>

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
  });
</script>
</div>
