<table class="catalogue_table">
  <thead class="workflow">
    <tr><th colspan=4><?php echo __("Latest Statuses") ; ?> :</th></tr>
    <tr>
      <th><?php echo __('Date');?></th>
      <th><?php echo __('Status');?></th>
      <th><?php echo __('Comments');?></th>
      <th><?php echo __('By');?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($loanstatus as $info) : ?>
    <tr>
      <td><?php $date = new DateTime($info->getModificationDateTime());
      		echo $date->format('d/m/Y H:i:s'); ?></td>
      <td><?php echo $info->getFormattedStatus();?></td>
      <td><?php echo $info->getComment();?></td>
      <td><?php echo $info->Users->__toString() ;?></td>      
    </tr>
    <?php endforeach ; ?>
    <?php if ($loanstatus->count() == 5 ) : ?>
    <tr>
      <td colspan="3">&nbsp;</td>
      <td>
        <a class="link_catalogue" information="true" title="<?php echo __('view all workflows');?>" href="<?php echo url_for('loan/viewAll?table='.$table.'&id='.$eid); ?>">
        <?php echo __('History');?></a>
      </td>
    </tr>   
    <?php endif ; ?>   
  </tbody>
</table>
