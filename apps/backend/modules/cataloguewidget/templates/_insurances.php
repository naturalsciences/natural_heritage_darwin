<table class="catalogue_table">
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
	  <a class="link_catalogue" title="<?php echo __('Edit (insurances) Values');?>" href="<?php echo url_for('insurances/add?table='.$table.'&rid='.$insurance->getId().'&id='.$eid); ?>">
	    <?php echo $insurance->getFormatedInsuranceValue();?>
	  </a>
      </td>
      <td><?php echo $insurance->getDateFromMasked(ESC_RAW);?></td>
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
          <?php echo $insurance->Contact->getFamilyName();?>
        <?php else: ?>
          <?php echo '-'; ?>
        <?php endif; ?>
      </td>      
      <td class="widget_row_delete">
        <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=insurances&id='.$insurance->getId());?>" title="<?php echo __('Are you sure ?') ?>"><?php echo image_tag('remove.png'); ?>
        </a>
      </td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>

<br />
<?php echo image_tag('add_green.png');?><a title="<?php echo __('Add (insurance) Value');?>" class="link_catalogue" href="<?php echo url_for('insurances/add?table='.$table.'&id='.$eid); ?>"><?php echo __('Add');?></a>
