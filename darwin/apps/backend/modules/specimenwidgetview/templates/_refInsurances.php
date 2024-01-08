<?php foreach($Insurances as $insurance):?>
<table  class="catalogue_table_view">
  <thead>
    <tr>
      <th>
        <?php echo __('Date from'); ?>
      </th>      
      <th>
        <?php echo __('Date to'); ?>
      </th>   
      <th>
        <?php echo __('Value'); ?>
      </th>         
      <th>
        <?php echo __('Currency'); ?>
      </th>
    </tr>
  </thead>
  <tbody  class="parts_insurances_data"">
    <tr>
      <td class="datesNum"><?php echo $insurance->getDateFromMasked(ESC_RAW);?></td> 
      <td class="datesNum"><?php echo $insurance->getDateToMasked(ESC_RAW);?></td>       
      <td>
        <?php echo $insurance->getInsuranceValue();?>
      </td>
      <td>
        <?php echo $insurance->getInsuranceCurrency();?>
      </td>
    </tr>
    <tr>
      <th>
      	<?php echo __('Insurer');?>
      </th>     
      <td colspan="3">
         <?php echo $insurance->getInsurerRef() == ''?'-':$insurance->People->getFormatedName() ; ?>
      </td>    
    </tr>
    <tr>
      <th>
      	<?php echo __('Contact');?>
      </th>         
      <td colspan="3">
         <?php echo $insurance->getContactRef() == ''?'-':$insurance->Contact->getFormatedName() ; ?>
      </td>       
    </tr>
	<tr>
      <th>
      	<?php echo __('Disaster recovery score');?>
      </th>         
      <td colspan="3">
         <?php echo $insurance->getDisasterRecoveryScore() ; ?>
      </td>       
    </tr>
  </tbody>
</table>
<?php endforeach;?>

