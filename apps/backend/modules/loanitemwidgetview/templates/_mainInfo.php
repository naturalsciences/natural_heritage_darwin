<table class="table_main_info">
  <tbody>
    <tr>
      <th><?php echo __('Part Id');?></th>
      <td><?php echo $loan->getPartRef();?></td>
      <th><?php echo __('Details');?></th>
      <td rowspan="3" class="loanitem_details"><?php echo $loan->getDetails();?></td>
    </tr>
    <tr>
      <th><?php echo __('I.G. Number');?></th>
      <td><?php echo $loan->Ig->getIgNum();?></td>
    </tr>
    <tr>
      <th><?php echo __('Return Date');?></th>
      <td><?php echo $loan->getToDate();?></td>
    </tr>
  </tbody>
</table>