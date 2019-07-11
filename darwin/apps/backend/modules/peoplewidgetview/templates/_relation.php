<table class="catalogue_table_view">
  <thead>
    <tr>
      <th></th>
      <th></th>
      <th><?php echo __('Relation');?></th>
      <th></th>
      <th><?php echo __('Role');?></th>
      <th class="datesNum"><?php echo __('Period');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($relations as $relation):?>
    <tr class="rid_<?php echo $relation->Parent->getId();?>">
    <td><?php echo image_tag('info.png',"title=info class='info lid_1'");?></td>
    <td>
    	<?php echo $relation->Child;?>     
    </td>
    <td><?php echo $relation->getRelationshipType();?></td>
    <td><?php echo $relation->Parent;?></td>
    <td><?php echo $relation->getPersonUserRole();?></td>
    <td class="datesNum">
	<?php echo $relation->getActivityDateFromObject()->getDateMasked('em','Y',ESC_RAW);?> - 
	<?php echo $relation->getActivityDateToObject()->getDateMasked('em','Y', ESC_RAW) ?>
    </td>    
  </tr>
  <?php endforeach;?>
  </tbody>
</table>
