<?php if($eid):?>
<table class="catalogue_table<?php if(isset($view)) echo '_view';?>">
  <thead>
    <tr>
      <th><?php echo __('Name');?></th>
      <th><?php echo __('Status');?></th>
      <th><?php echo __('From');?></th>
      <th><?php echo __('To');?></th>
      <th><?php echo __('Description');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
        <?php foreach($loans as $item):?>
          <tr class="rid_<?php echo $item->getId();?> <?php if(isset($status[$item->getId()]) && $status[$item->getId()]->getStatus() =='closed') echo 'loan_line_closed';?>">
            <td class="item_name"><?php echo $item->getName();?></td>
            <td class="loan_status_col"><?php if(isset($status[$item->getId()])):?>
                <?php echo $status[$item->getId()]->getFormattedStatus(); ?>
                <?php if($status[$item->getId()]->getStatus() =='closed'):?>
                  <em>(<?php echo __('on %date%',array('%date%'=> $status[$item->getId()]->getDate() ));?>)</em>
                <?php endif?>
              <?php endif?>
            </td>
            <td class="datesNum">
              <?php echo $item->getFromDateFormatted();?>
            </td>
            <td class="datesNum <?php if($item->getIsOverdue()) echo 'loan_overdue';?>">
              <?php if($item->getExtendedToDateFormatted() != ''):?>
                <?php echo $item->getExtendedToDateFormatted();?>
              <?php else:?>
                <?php echo $item->getToDateFormatted();?>
              <?php endif;?>
            </td>
            <td>
              <?php echo $item->getDescription();?>
            </td>
            <td class="">
              <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'loan/view?id='.$item->getId());?>
              <?php if(in_array($item->getId(),sfOutputEscaper::unescape($rights)) || $sf_user->isAtLeast(Users::ADMIN)) : ?>
              <?php echo link_to(image_tag('edit.png',array('title'=>__('Edit loan'))),'loan/edit?id='.$item->getId());?>
              <?php endif ; ?>
            </td>
          </tr>
          <tr class="hidden details details_rid_<?php echo $item->getId();?>" >
            <td colspan="8"></td>
          </tr>
        <?php endforeach;?>
  </tbody>
</table>

<?php else:?>
  <?php echo __('No Loans recorded yet');?>
<?php endif;?>
