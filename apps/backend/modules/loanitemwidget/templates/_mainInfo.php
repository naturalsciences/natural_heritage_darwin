<table class="table_main_info">
  <tbody>
    <?php echo $form->renderGlobalErrors() ?>
    <?php $obj = $form->getObject();?>
    <tr>
      <th><?php echo __('Part Id');?></th>
      <td><?php echo $obj->getPartRef();?></td>
      <th><?php echo __('Details');?></th>
      <td rowspan="3" class="loanitem_details"><?php echo $obj->getDetails();?></td>
    </tr>
    <tr>
      <th><?php echo __('I.G. Number');?></th>
      <td><?php  if($obj->getIgRef()) echo $obj->Ig->getIgNum();?></td>
    </tr>
    <tr>
      <th><?php echo __('Return Date');?></th>
      <td><?php echo $obj->getToDate();?></td>
    </tr>
  </tbody>
</table>