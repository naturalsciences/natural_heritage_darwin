<?php if($eid):?>
<table class="catalogue_table">
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
        <?php echo link_to($maintenance->getCategory(),'specimen/editMaintenance?id='.$maintenance->getId(),array('class'=>"link_catalogue",'title'=> __('Edit Maintenance'))); ?>
      </td>
      <td><?php echo $maintenance->getActionObservation();?></td>
      <td class="datesNum"><?php echo $maintenance->getModificationDateTimeMasked(ESC_RAW);?></td>
      <td><?php echo ($maintenance->getPeopleRef()?$maintenance->People->getFormatedName():'-');?></td>
      <td><?php echo $maintenance->getDescription();?></td>
      <td class="widget_row_delete">
        <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=collection_maintenance&id='.$maintenance->getId());?>" title="<?php echo __('Are you sure ?') ?>"><?php echo image_tag('remove.png'); ?></a>
      </td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>
<?php echo image_tag('add_green.png');?>
<a title="<?php echo __('Add Maintenance');?>" class="link_catalogue" href="<?php echo url_for('specimen/editMaintenance?table=specimens&rid='.$eid); ?>"><?php echo __('Add Maintenance');?></a>
<br />
<?php else:?>
  <?php echo __('Please save your specimen in order to add maintenances');?>
<?php endif;?>
