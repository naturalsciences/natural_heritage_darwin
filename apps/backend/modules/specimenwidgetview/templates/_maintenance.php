<?php if($eid):?>
<table class="catalogue_table_view">
  <thead>
    <tr>
      <th><?php echo __('Type');?></th>
      <th><?php echo __('Action / Observation');?></th>
      <th><?php echo __('Date');?></th>
      <th><?php echo __('People');?></th>
      <th><?php echo __('Description');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($maintenances as $maintenance):?>
    <tr>
      <td>
        <?php echo $maintenance->getCategory(); ?>
      </td>
      <td><?php echo $maintenance->getActionObservation();?></td>
      <td class="datesNum"><?php echo $maintenance->getModificationDateTimeMasked(ESC_RAW);?></td>
      <td><?php if($maintenance->People):?>
        <?php echo $maintenance->People->getFormatedName();?>
      <?php endif;?></td>
      <td><?php echo $maintenance->getDescription();?></td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>

<br />
<?php else:?>
  <?php echo __('Please save your part and use the "mass action" in order to add maintenances');?>
<?php endif;?>
