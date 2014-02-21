<table class="catalogue_table_view">
  <tr>
	<th><?php echo __("supernumerary ?");?></th>
	<td><?php echo ($spec->getSurnumerary()?__('Yes'):__('No')); ?></td>
  </tr>

  <tr>
	<th><?php echo __("Container");?></th>
	<td><?php echo $spec->getContainer()==''?'-':$spec->getContainer() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo __("Container type");?></th>
	<td><?php echo $spec->getContainerType()==''?'-':$spec->getContainerType() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo __("Container storage");?></th>
	<td><?php echo $spec->getContainerStorage()==''?'-':$spec->getContainerStorage() ?></td>
  </tr>
  <tr>
	<th><?php echo __("Sub container");?></th>
	<td><?php echo $spec->getSubContainer()==''?'-':$spec->getSubContainer() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo __("Sub Container Type");?></th>
	<td><?php echo $spec->getSubContainerType()==''?'-':$spec->getSubContainerType() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo __("Sub container storage");?></th>
	<td><?php echo $spec->getSubContainerStorage()==''?'-':$spec->getSubContainerStorage() ?></td>
  </tr>
</table>
