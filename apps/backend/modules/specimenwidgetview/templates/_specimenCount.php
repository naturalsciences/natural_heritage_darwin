<table class="catalogue_table_view">
  <tr>
	  <th class="top_aligned"><?php echo __("Accuracy");?></th>
	  <td><?php echo $accuracy ?></td>
  </tr>
  <tr>
  	<th><?php echo $accuracy=='Exact'?__("Specimen part count"):__("Specimen part count min");?></th>
  	<td><?php echo $spec->getSpecimenCountMin() ; ?></td>
  </tr>
  <?php if($accuracy!='Exact') : ?>
  <tr>
	  <th><?php echo __("Specimen part count max");?></th>
  	<td><?php echo $spec->getSpecimenCountMax() ; ?></td>
  </tr>
  <?php endif ; ?>
</table>
