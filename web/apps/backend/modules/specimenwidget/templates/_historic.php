<?php if($eid):?>
<table class="catalogue_table<?php if(isset($view)) echo '_view';?>">
  <thead>
    <tr>
      <th><?php echo __('Date');?></th>
      <th><?php echo __('Person');?></th>

      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($items as $item):?>
    <tr>
      <td><?php $date = new DateTime($item['modification_date_time']);
    echo $date->format('Y/m/d H:i'); ?></td>
      <td><?php echo $item['Users'];?></td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>

<?php else:?>
  <?php echo __('No modifications recorded yet');?>
<?php endif;?>
