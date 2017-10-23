<table class="catalogue_table_view">
  <thead>
    <tr>
      <th><?php echo __('Value');?></th>
      <th><?php echo __('Date from');?></th>
      <th><?php echo __('Date to');?></th>
      <th><?php echo __('Insurer');?></th>
      <th><?php echo __('Person of contact'); ?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($insurances as $insurance):?>
    <tr>
      <td>
  	    <?php echo $insurance->getFormatedInsuranceValue();?>
      </td>
      <td><?php echo $insurance->getDateFromMasked(ESC_RAW) ; ?></td>
      <td><?php echo $insurance->getDateToMasked(ESC_RAW);?></td>       		
      <td>
        <?php if($insurance->People): ?>
          <?php echo $insurance->People->getFamilyName();?>
        <?php else: ?>
          <?php echo '-'; ?>
        <?php endif; ?>
      </td>
      <td>
        <?php if($insurance->Contact): ?>
          <?php echo $insurance->Contact->getFormatedName();?>
        <?php else: ?>
          <?php echo '-'; ?>
        <?php endif; ?>
      </td>      
    </tr>
    <?php endforeach;?>
  </tbody>
</table>
