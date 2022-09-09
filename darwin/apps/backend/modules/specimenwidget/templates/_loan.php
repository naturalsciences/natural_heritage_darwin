<?php if($eid):?>
<table class="catalogue_table<?php if(isset($view)) echo '_view';?>">
  <thead>
    <tr>
      <th><?php echo __('Name');?></th>
      <th><?php echo __('Status');?></th>
      <th><?php echo __('From');?></th>
      <th><?php echo __('To');?></th>
      <th><?php echo __('Institution');?></th>
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
                <?php echo $item->getInstitutionReceiver();?>
             </td>
             
            
            <td class="">
              <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'loan/view?id='.$item->getId());?>
              <?php if(in_array($item->getId(),sfOutputEscaper::unescape($rights)) || $sf_user->isAtLeast(Users::ADMIN)) : ?>
              <?php echo link_to(image_tag('edit.png',array('title'=>__('Edit loan'))),'loan/edit?id='.$item->getId());?>
              <?php endif ; ?>
            </td>
          </tr>
          
              <?php echo $item->getDescription();?>
             
              <?php if(array_key_exists($item->getId(),$loan_properties->getRawValue())): ?>
              <thead>
                <tr><th>Details:</th></tr>
              </thead>
                 <?php foreach($loan_properties->getRawValue() as $loan_props) :?>
                    <?php foreach($loan_props as $loan_prop) :?>
                    <tr>
                        <td><?php print($loan_prop->getPropertyType());?></td>
                        <td><?php print($loan_prop->getLowerValue());?></td>
                        <td><?php print($loan_prop->getFromDateMasked(ESC_RAW));?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
               <?php endif;?>
          <tr class="hidden details details_rid_<?php echo $item->getId();?>" >
            <td colspan="8"></td>
          </tr>
        <?php endforeach;?>
		 
  </tbody>
</table>
<br/>
<?php if(count($items)>0):?>
<h4><?php echo __("Partial returns");?></h4>
<table class="catalogue_table<?php if(isset($view)) echo '_view';?>">

 <thead>
 <th><?php echo __('Name');?></th>
 <th><?php echo __('Status');?></th>
 <th><?php echo __('To');?></th>
 </thead>
 <tbody>
<?php foreach($items as $name=> $item):?>
			<tr>
				<td><?php print($name);?></td>
				<td><?php print("partial return");?></td>
				<td><?php print($item->getToDate());?></td>
			</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif;?>



<?php else:?>
  <?php echo __('No Loans recorded yet');?>
<?php endif;?>
