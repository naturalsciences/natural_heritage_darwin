<table class="catalogue_table_view">
  <tr>
	  <th class="top_aligned"><?php echo __("Accuracy");?></th>
	  <td><?php echo $accuracy ?></td>
  </tr>
  <tr>
  	<th><?php echo $accuracy=='Exact'?__("Specimen individuals count"):__("Specimen individuals count min");?></th>
  	<td><?php echo $indiv->getSpecimenIndividualsCountMin() ; ?></td>
  </tr>
  <?php if($accuracy!='Exact') : ?>
  <tr>
	  <th><?php echo __("Specimen individuals count max");?></th>
  	<td><?php echo $indiv->getSpecimenIndividualsCountMax() ; ?></td>
  </tr>
  <?php endif ; ?>
</table>
