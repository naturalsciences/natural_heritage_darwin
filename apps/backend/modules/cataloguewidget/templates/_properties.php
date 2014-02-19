<table class="catalogue_table">
  <thead>
    <tr>
      <th><?php echo __('Type');?></th>
      <th><?php echo __('Applies to');?></th>
      <th><?php echo __('Values');?></th>      
      <th><?php echo __('Date From');?></th>
      <th><?php echo __('Date To');?></th>
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
      <td><?php echo $property->getAppliesTo();?></td>
      <td>
        <?php echo $property->getLowerValue();?>
        <?php if($property->getUpperValue() != ''):?>
          -> <?php echo $property->getUpperValue();?>
        <?php endif;?>
        <?php echo $property->getPropertyUnit();?>

        <?php if($property->getPropertyAccuracy() != ''):?>
          ( +- <?php echo $property->getPropertyAccuracy();?> <?php echo $property->getPropertyUnit();?>)
        <?php endif;?>

      </td>
      <td class="datesNum"><?php echo $property->getFromDateMasked(ESC_RAW);?></td>
      <td class="datesNum"><?php echo $property->getToDateMasked(ESC_RAW);?></td>      
      <td class="widget_row_delete">
        <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=properties&id='.$property->getId());?>" title="<?php echo __('Delete Properties') ?>"><?php echo image_tag('remove.png'); ?>
        </a>
      </td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>

<br />
<?php echo image_tag('add_green.png');?>
<a title="<?php echo __('Add Properties');?>" class="link_catalogue" href="<?php echo url_for('property/add?table='.$table.'&id='.$eid); ?>">
<?php if(count(Properties::getModels($table)) > 1):?>
  <?php echo __("Add Properties with this pre defined template") ; ?> :</a>
  <select id='property_template'>
    <?php foreach(Properties::getModels($table) as $key=>$values)
      echo "<option value=\"$key\">$values" ;?>
  </select>
<?php else :?>
 <?php echo __('Add Properties');?></a>
<?php endif;?>
<script>
  $('a.link_catalogue').click(function() {
    if($('#property_template').val() != "")
    {
      $(this).attr('href',$(this).attr('href')+"/model/"+$('#property_template').val()) ;
    }
  });
</script>
