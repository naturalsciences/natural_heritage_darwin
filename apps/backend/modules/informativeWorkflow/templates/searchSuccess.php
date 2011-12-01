 <?php if(isset($items) && $items->count() != 0 && isset($orderBy) && isset($orderDir) && isset($currentPage)):?>
    <?php
      if($orderDir=='asc')
        $orderSign = '<span class="order_sign_down">&nbsp;&#9660;</span>';
      else
        $orderSign = '<span class="order_sign_up">&nbsp;&#9650;</span>';
    ?>
<?php use_helper('Text');?>    
<table class="catalogue_table_view">
  <thead class="workflow">
    <tr><th colspan=4><?php echo __("Latest Status") ; ?></th></tr>
    <tr>
      <th><?php echo __('Date');?></th>
      <th><?php echo __("Relation");?></th>
      <th><?php echo __('Status');?></th>
      <th><?php echo __('Comments');?></th>
      <th></th>      
    </tr>
  </thead>
  <tbody>
    <?php foreach($items as $info) : ?>
    <tr>
      <td><?php $date = new DateTime($info['modification_date_time']);
      		echo $date->format('Y/m/d H:i:s'); ?></td>
      <td><?php echo $info['referenced_relation']; ?></td>
      <td><?php echo __($info['formattedStatus']) ;?></td>
      <td><?php echo truncate_text($info['comment'],30);?></td>
      <td>
        <?php echo image_tag('info.png', 'class=more_comment');?>
        <ul class="field_change">
            <?php echo $info['comment'];?>     
        </ul>
        <?php echo link_to(image_tag('next.png'), $info->getLink());?>
      </td>
    </tr>
    <?php endforeach ; ?>   
  </tbody>
</table>
<script type="text/javascript">
$(document).ready(function()
{
  $('img.more_comment').each(function()
  {
    $(this).qtip(
    {
     content: $(this).next().html(),
     delay: 100,
     show: { solo: true},
     position: { my : 'bottom right',target: 'mouse'}
    });
  });
});
</script>
 <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
<?php endif;?>
