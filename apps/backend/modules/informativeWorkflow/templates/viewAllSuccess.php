<table class="catalogue_table">
  <thead  class="workflow">
    <tr><th colspan=4><?php echo __("All associated workflows") ; ?></th></tr>
    <tr>
      <th><?php echo __('Date');?></th>
      <th><?php echo __('Status');?></th>
      <th><?php echo __('Comments');?></th>
      <th><?php echo __('By');?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($informativeWorkflow as $info) : ?>
    <tr>
      <th><?php $date = new DateTime($info->getModificationDateTime());
      		echo $date->format('d/m/Y H:i:s'); ?></th>
      <th><?php echo $info->getStatus();?></th>
      <th><?php echo $info->getComment();?></th>
      <th><?php echo $info->getUserRef()?$info->Users->__toString():$info->getFormatedName();?></th>      
    </tr>
    <?php endforeach ; ?>     
  </tbody>
</table>
