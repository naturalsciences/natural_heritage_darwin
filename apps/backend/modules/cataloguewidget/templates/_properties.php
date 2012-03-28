<table class="catalogue_table">
  <thead>
    <tr>
      <th><?php echo __('Type');?></th>
      <th><?php echo __('Sub-Type');?></th>
      <th><?php echo __('Qualifier');?></th>
      <th><?php echo __('Date From');?></th>
      <th><?php echo __('Date To');?></th>
      <th><?php echo __('Values');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($properties as $property):?>
    <tr>
      <td>  
    	  <a class="link_catalogue" title="<?php echo __('Edit Properties');?>" href="<?php echo url_for('property/add?table='.$table.'&rid='.$property->getId().'&id='.$eid); ?>">
	        <?php echo $property->getPropertyType();?>
    	  </a>
      </td>
      <td><?php echo $property->getPropertySubType();?></td>
      <td><?php echo $property->getPropertyQualifier();?></td>
      <td class="datesNum"><?php echo $property->getFromDateMasked(ESC_RAW);?></td>
      <td class="datesNum"><?php echo $property->getToDateMasked(ESC_RAW);?></td>
      <td>
	<?php if( count($property->PropertiesValues) > 1):?>
	  <a href="#" class="display_value"><?php echo format_number_choice('[1]Show 1 Value|(1,+Inf]Show %1% Values', array('%1%' => count($property->PropertiesValues) ), count($property->PropertiesValues));?></a>
	  <a href="#" class="hidden hide_value"><?php echo __('Hide Values');?></a>
	  <ul class="hidden">
	    <?php foreach($property->PropertiesValues as $value):?>
	      <li>
		<?php echo $value->getPropertyValue();?> <?php echo $property->getPropertyUnit();?> 
		<?php if($value->getPropertyAccuracy() != ""):?>
		  ( +- <?php echo $value->getPropertyAccuracy();?> <?php echo $property->getPropertyAccuracyUnit();?>)
		<?php endif;?>
	      </li>
	    <?php endforeach;?>    
	  </ul>
	<?php elseif(count($property->PropertiesValues) == 1):?>
	  <ul><li><?php echo $property->PropertiesValues[0]->getPropertyValue();?> <?php echo $property->getPropertyUnit();?> 
	    <?php if($property->PropertiesValues[0]->getPropertyAccuracy() != ""):?>
	      ( +- <?php echo $property->PropertiesValues[0]->getPropertyAccuracy();?> <?php echo $property->getPropertyAccuracyUnit();?>)
	    <?php endif;?></li></ul>
	<?php else:?>
	  <?php echo __('No Values');?>
	<?php endif;?>
      </td>
      <td class="widget_row_delete">
        <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=catalogue_properties&id='.$property->getId());?>" title="<?php echo __('Delete Properties') ?>"><?php echo image_tag('remove.png'); ?>
        </a>
      </td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>

<script>
$('.display_value').click(showValues);
$('.hide_value').click(hideValues);
</script>
<br />
<?php echo image_tag('add_green.png');?>
<a title="<?php echo __('Add Properties');?>" class="link_catalogue" href="<?php echo url_for('property/add?table='.$table.'&id='.$eid); ?>"><?php echo __('Add property');?></a> 
<?php echo __("with this pre defined template") ; ?>:
<select id='property_template'>
<?php 
  foreach(CatalogueProperties::getModels() as $key=>$values) echo "<option value=\"$key\">$values" ;
?>
</select>

<script>
  $('a.link_catalogue').click(function() {
    if($('#property_template').val() != "")
    {
      $(this).attr('href',$(this).attr('href')+"/model/"+$('#property_template').val()) ;
    }
  });
</script>
