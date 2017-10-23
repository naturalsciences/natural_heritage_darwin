<table class="catalogue_table_view">
  <thead>
    <tr>
      <th><?php echo __('Type');?></th>
      <th><?php echo __('Applies to');?></th>
      <th><?php echo __('Values');?></th>
      <th class="datesNum"><?php echo __('Date From');?></th>
      <th class="datesNum"><?php echo __('Date To');?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($properties as $property):?>
    <tr>
      <td><?php echo $property->getPropertyType();?></td>
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
    </tr>
    <?php endforeach;?>
  </tbody>
</table>
