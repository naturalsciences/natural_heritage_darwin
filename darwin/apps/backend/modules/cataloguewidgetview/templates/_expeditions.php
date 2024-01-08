<?php use_helper('Text');?>
<table class="catalogue_table_view">
  <thead>
    <tr>
      <th><?php echo __('Id');?></th>
      <th><?php echo __('Name');?></th>
	  <th><?php echo __('Count records');?></th>
	  <th><?php echo __('Specimen count (min)');?></th>
	  <th><?php echo __('Specimen count (max)');?></th>
	   <th><?php echo __('Types');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php 
	$sum_rec=0; $sum_min=0; $sum_max=0;?>
  
  
  <?php foreach($expeditions as $exp):?>
  <tr>
    <td>
	   <a  target="_blank"  href="<?php echo url_for('expedition/view?id='.$exp["expedition_ref"]);?>"><?php echo $exp["expedition_ref"];?></a>
    
    </td>
	<td>
      <?php echo $exp["expedition_name"];?>
    </td>
	<td>
      <?php echo $exp["count_records"];?>
    </td>
	<td>
      <?php echo $exp["specimen_count_min"];?>
    </td>
	<td>
      <?php echo $exp["specimen_count_max"];?>
    </td>
	<td>
      <?php echo $exp["taxo_types"];?>
    </td>
      <?php $sum_rec=$sum_rec+$exp["count_records"]; $sum_min=$sum_min+$exp["specimen_count_min"]; $sum_max=$sum_max+$exp["specimen_count_max"];?>
  </tr>
  <?php endforeach;?>
     <td colspan=6><hr/></td>
	 <tr>
    <td>
      
    </td>
	<td>
    
    </td>
	<td>
      <?php echo $sum_rec;?>
    </td>
	<td>
      <?php echo $sum_min;?>
    </td>
	<td>
      <?php echo $sum_max;?>
    </td>
      <?php $sum_rec=$sum_rec+$exp["count_records"]; $sum_min=$sum_min+$exp["specimen_count_min"]; $sum_max=$sum_max+$exp["specimen_count_max"];?>
  </tr>
  </tbody>
</table>
