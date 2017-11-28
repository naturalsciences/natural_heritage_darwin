<table>
<?php foreach($relations as $relation):?>
  <tr class="rid_<?php echo $relation->Parent->getId();?> detail_<?php echo $relation->Child->getId();?>">
    <td><?php echo image_tag('next.png');?></td>
    <td><?php echo $relation->getRelationshipType();?></td>
    <td><?php echo $relation->Parent;?></td>
    <td>(<?php echo $relation->getPersonUserRole();?>)</td>
  </tr>
<?php endforeach;?>
</table>
