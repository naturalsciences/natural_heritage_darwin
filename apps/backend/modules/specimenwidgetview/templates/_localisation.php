<table class="catalogue_table_view">

  <tr>
  <th class="top_aligned"><?php echo __("Institution");?></th>
  <td><?php echo $spec->getInstitutionRef()==''?'-':$spec->getInstitution()->getFormatedName() ?></td>
  </tr>
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
  <th class="top_aligned"><?php echo __("Column");?></th>
  <td><?php echo $spec->getCol()==''?'-':$spec->getCol() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo __("Shelf");?></th>
	<td><?php echo $spec->getShelf()==''?'-':$spec->getShelf() ?></td>
  </tr>
</table>
