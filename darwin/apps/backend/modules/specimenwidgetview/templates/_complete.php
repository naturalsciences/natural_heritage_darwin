<table class="catalogue_table_view">
  <tr>
	<th class="top_aligned"><?php echo __("Status");?></th>
	<td><?php echo $spec->getSpecimenStatus() ?></td>
  </tr>
  <tr>
	<th><?php echo __("Complete ?");?></th>
	<td><?php echo ($spec->getcomplete()?__("Yes"):__("No")) ; ?></td>
  </tr>
</table>
