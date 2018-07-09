<table class="catalogue_table_view" style="border-style: solid;">
	<!--JM Herpers 2018 06 8-->
	<?php if($spec->getSpecimenCountMin() != 0) : ?>
		<tr>
			<th width="20%"><?php echo "Total";?></th>
		</tr>
		<tr>
			<td width="20%">&nbsp;&nbsp;<b><?php echo "Count";?></b></td>
			<td>
				<?php echo $spec->getSpecimenCountMin() ;
					  if($accuracy!='Exact') : 
						echo "-".$spec->getSpecimenCountMax() ;
					  endif ; ?>
			</td>
		</tr>
		<tr>
			<td class="top_aligned" width="20%">&nbsp;&nbsp;&nbsp;<b><?php echo __("Accuracy");?></b></td>
			<td><?php echo $accuracy?></td>
		</tr>
	<?php endif ; ?>
	  <!--
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
	  <?php endif ; ?>-->
 </table>
 <br/>
  <!--ftheeten 2016 06 22-->
  <table class="catalogue_table_view" style="border-style: solid;">
	<!--JM Herpers 2018 06 8-->
	<?php if($spec->getSpecimenCountMalesMin() != 0) : ?>
		<tr>
			<th width="20%"><?php echo "Males";?></th>
		</tr>
		<tr>
			<td width="20%">&nbsp;&nbsp;<b><?php echo "Count";?></b></td>
			<td>
				<?php echo $spec->getSpecimenCountMalesMin() ;
					  if($accuracy_males!='Exact') : 
						echo "-".$spec->getSpecimenCountMalesMax() ;
					  endif ; ?>
			</td>
		</tr>
		<tr>
			<td class="top_aligned" width="20%">&nbsp;&nbsp;&nbsp;<b><?php echo __("Accuracy");?></b></td>
			<td><?php echo $accuracy_males ?></td>
		</tr>
	<?php endif ; ?>
  <!--
	<tr>
		<th width="50%"><?php echo $accuracy_males=='Exact'?__("Specimen males count"):__("Specimen males count min");?></th>
		<th width="50%"><?php echo "count";?></th>
		<td><?php echo $spec->getSpecimenCountMalesMin() ; ?></td>
	</tr>
	<tr>
		  <th class="top_aligned" width="50%"><?php echo __("Accuracy males");?></th>
		  <td><?php echo $accuracy_males ?></td>
	</tr>
	<?php if($accuracy_males!='Exact') : ?>
	  <tr>
		  <th width="50%"><?php echo __("Specimen males count max");?></th>
		<td><?php echo $spec->getSpecimenCountMalesMax() ; ?></td>
	  </tr>
	<?php endif ; ?>-->
  </table>
   <!--ftheeten 2016 06 22-->
  <br/>
  <table class="catalogue_table_view" style="border-style: solid;">
  <!--JM Herpers 2018 06 8-->
	<?php if($spec->getSpecimenCountFemalesMin() != 0) : ?>
		<tr>
			<th width="20%"><?php echo "Females";?></th>
		</tr>
		<tr>
			<td width="20%">&nbsp;&nbsp;<b><?php echo "Count";?></b></td>
			<td>
				<?php echo $spec->getSpecimenCountFemalesMin() ;
					  if($accuracy_females!='Exact') : 
						echo "-".$spec->getSpecimenCountFemalesMax() ;
					  endif ; ?>
			</td>
		</tr>
		<tr>
			<td class="top_aligned" width="20%">&nbsp;&nbsp;&nbsp;<b><?php echo __("Accuracy");?></b></td>
			<td><?php echo $accuracy_females ?></td>
		</tr>
	<?php endif ; ?>
  <!--
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
  <?php endif ; ?>-->
</table>
 <!--ftheeten 2016 06 22-->
  <br/>
  <table class="catalogue_table_view" style="border-style: solid;">
  <!--JM Herpers 2018 06 8-->
	<?php if($spec->getSpecimenCountJuvenilesMin() != 0) : ?>
		<tr>
			<th width="20%"><?php echo "Juveniles";?></th>
		</tr>
		<tr>
			<td width="20%">&nbsp;&nbsp;<b><?php echo "Count";?></b></td>
			<td>
				<?php echo $spec->getSpecimenCountJuvenilesMin() ;
					  if($accuracy_juveniles!='Exact') : 
						echo "-".$spec->getSpecimenCountJuvenilesMax() ;
					  endif ; ?>
			</td>
		</tr>
		<tr>
			<td class="top_aligned" width="20%">&nbsp;&nbsp;&nbsp;<b><?php echo __("Accuracy");?></b></td>
			<td><?php echo $accuracy_juveniles ?></td>
		</tr>
	<?php endif ; ?>
  <!--
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
  <?php endif ; ?>-->
  </table>

