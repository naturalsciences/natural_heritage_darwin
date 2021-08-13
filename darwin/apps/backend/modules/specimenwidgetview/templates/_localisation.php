<table class="catalogue_table_view">
  <?php $pattern=""; ?>
   <?php $pattern=$spec->getInstitutionRef()==''?$pattern:$pattern.'&specimen_search_filters[institution_ref]='.$spec->getInstitutionRef();?>
  <tr>
  <th class="top_aligned"><?php echo __("Institution");?></th>
  <td><?php echo $spec->getInstitutionRef()==''?'-':$spec->getInstitution()->getFormatedName() ?></td>
  </tr>
  <?php $pattern=$spec->getBuilding()==''?$pattern:$pattern.'&specimen_search_filters[building]='.$spec->getBuilding();?>
  <tr>
	<th class="top_aligned"><?php echo __("Building");?></th>
	<td><?php echo $spec->getBuilding()==''?'-':$spec->getBuilding() ?></td>
  </tr>
   <?php $pattern=$spec->getFloor()==''?$pattern:$pattern.'&specimen_search_filters[floor]='.$spec->getFloor();?>
  <tr>
	<th class="top_aligned"><?php echo __("Floor");?></th>
	<td><?php echo $spec->getFloor()==''?'-':link_to($spec->getFloor(), url_for("specimensearch/search")."?submit=Search&".$pattern, array('target' => '_blank')) ?></td>
  </tr>
   <?php $pattern=$spec->getRoom()==''?$pattern:$pattern.'&specimen_search_filters[room]='.$spec->getRoom();?>
  <tr>
	<th class="top_aligned"><?php echo __("Room");?></th>
	<td><?php echo $spec->getRoom()==''?'-':link_to($spec->getRoom(), url_for("specimensearch/search")."?submit=Search&".$pattern, array('target' => '_blank')) ?></td>
  </tr>
 <?php $pattern=$spec->getRow()==''?$pattern:$pattern.'&specimen_search_filters[row]='.$spec->getRow();?>
  <tr>
	<th class="top_aligned"><?php echo __("Row");?></th>
	<td><?php echo $spec->getRow()==''?'-':link_to($spec->getRow(), url_for("specimensearch/search")."?submit=Search&".$pattern, array('target' => '_blank')) ?></td>
  </tr>
   <?php $pattern=$spec->getCol()==''?$pattern:$pattern.'&specimen_search_filters[col]='.$spec->getCol();?>
  <tr>
  <th class="top_aligned"><?php echo __("Column");?></th>
  <td><?php echo $spec->getCol()==''?'-':link_to($spec->getCol(), url_for("specimensearch/search")."?submit=Search&".$pattern, array('target' => '_blank')) ?></td>
  </tr>
   <?php $pattern=$spec->getShelf()==''?$pattern:$pattern.'&specimen_search_filters[shelf]='.$spec->getShelf();?>
  <tr>
	<th class="top_aligned"><?php echo __("Shelf");?></th>
	<td><?php echo $spec->getShelf()==''?'-':link_to($spec->getShelf(), url_for("specimensearch/search")."?submit=Search&".$pattern, array('target' => '_blank')) ?></td>
  </tr>
</table>
