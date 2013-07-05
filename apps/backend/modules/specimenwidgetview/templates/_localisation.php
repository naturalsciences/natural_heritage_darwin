<table class="catalogue_table_view">
<?php if ($spec->getBuilding() != '' && $spec->getFloor() !='' && $spec->getRoom()!='' && $spec->getRow()!='' && $spec->getShelf() != '') : ?>
  <tr>
	<th class="top_aligned"><?php echo __("Building");?></th>
	<td><?php echo $spec->getBuilding()==''?'-':$spec->getBuilding() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo __("Floor");?></th>
	<td><?php echo $spec->getFloor()==''?'-':$spec->getFloor() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo __("Room");?></th>
	<td><?php echo $spec->getRoom()==''?'-':$spec->getRoom() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo __("Row");?></th>
	<td><?php echo $spec->getRow()==''?'-':$spec->getRow() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo __("Shelf");?></th>
	<td><?php echo $spec->getShelf()==''?'-':$spec->getShelf() ?></td>
  </tr>
<?php endif ; ?>
</table>
