<table class="catalogue_table_view" style="border-style: solid;">
  <tr>
	  <th class="top_aligned" width="50%"><?php echo __("Accuracy");?></th>
	  <td><?php echo $accuracy ?></td>
  </tr>
  <tr>
  	<th width="50%"><?php echo $accuracy=='Exact'?__("Specimen part count"):__("Specimen part count min");?></th>
  	<td><?php echo $spec->getSpecimenCountMin() ; ?></td>
  </tr>
  <?php if($accuracy!='Exact') : ?>
  <tr>
	  <th width="50%"><?php echo __("Specimen part count max");?></th>
  	<td><?php echo $spec->getSpecimenCountMax() ; ?></td>
  </tr>
  <?php endif ; ?>
 </table>
 <br/>
  <!--ftheeten 2016 06 22-->
  <table class="catalogue_table_view" style="border-style: solid;">
  <tr>
	  <th class="top_aligned" width="50%"><?php echo __("Accuracy males");?></th>
	  <td><?php echo $accuracy_males ?></td>
  </tr>
  <tr>
  	<th width="50%"><?php echo $accuracy_males=='Exact'?__("Specimen males count"):__("Specimen males count min");?></th>
  	<td><?php echo $spec->getSpecimenCountMalesMin() ; ?></td>
  </tr>
  <?php if($accuracy_males!='Exact') : ?>
  <tr>
	  <th width="50%"><?php echo __("Specimen males count max");?></th>
  	<td><?php echo $spec->getSpecimenCountMalesMax() ; ?></td>
  </tr>
  
  <?php endif ; ?>
  </table>
   <!--ftheeten 2016 06 22-->
  <br/>
  <table class="catalogue_table_view" style="border-style: solid;">
  <tr>
	  <th class="top_aligned" width="50%"><?php echo __("Accuracy females");?></th>
	  <td><?php echo $accuracy_females ?></td>
  </tr>
  <tr>
  	<th width="50%"><?php echo $accuracy_females=='Exact'?__("Specimen females count"):__("Specimen females count min");?></th>
  	<td><?php echo $spec->getSpecimenCountFemalesMin() ; ?></td>
  </tr>
  <?php if($accuracy_females!='Exact') : ?>
  <tr>
	  <th width="50%"><?php echo __("Specimen females count max");?></th>
  	<td><?php echo $spec->getSpecimenCountFemalesMax() ; ?></td>
  </tr>
  <?php endif ; ?>
</table>
 <!--ftheeten 2016 06 22-->
  <br/>
  <table class="catalogue_table_view" style="border-style: solid;">
  <tr>
	  <th class="top_aligned" width="50%"><?php echo __("Accuracy juveniles");?></th>
	  <td><?php echo $accuracy_juveniles ?></td>
  </tr>
  <tr>
  	<th width="50%"><?php echo $accuracy_juveniles=='Exact'?__("Specimen juveniles count"):__("Specimen juveniles count min");?></th>
  	<td><?php echo $spec->getSpecimenCountJuvenilesMin() ; ?></td>
  </tr>
  <?php if($accuracy_juveniles!='Exact') : ?>
  <tr>
	  <th width="50%"><?php echo __("Specimen juveniles count max");?></th>
  	<td><?php echo $spec->getSpecimenCountJuvenilesMax() ; ?></td>
  </tr>
  <?php endif ; ?>
  </table>
