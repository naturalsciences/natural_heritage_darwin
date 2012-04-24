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
      		echo $date->format('d/m/Y H:i:s'); ?></td>
      <td><?php echo $info['referenced_relation']; ?></td>
      <td><?php echo __($info['formattedStatus']) ;?></td>
      <td><?php echo truncate_text($info['comment'],30);?></td>
      <td>
        <?php echo image_tag('info.png', 'class=more_comment');?>
        <ul class="field_change">
            <?php echo $info['comment'];?>
        </ul>
        <?php echo link_to(image_tag('blue_eyel.png'), $info->getLink('view'));?>
        <?php echo link_to(image_tag('edit.png'), $info->getLink('edit'));?>
        <?php if($sf_user->isAtLeast(Users::MANAGER)):?>
          <?php echo link_to(image_tag('remove.png'), 'informativeWorkflow/delete?id='.$info->getId(),'class=workflow_delete');?>
        <?php endif;?>
      </td>
    </tr>
    <?php endforeach ; ?>   
  </tbody>
</table>
 <?php  if($items->count() > 10) include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
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
 
  $('.workflow_delete').click(function(event)
  {
    event.preventDefault();
    var answer = confirm('<?php echo addslashes(__('Are you sure ?'));?>');
    if(answer) {
      row = $(this).closest('tr');
      $.get($(this).attr('href'), function() {
        row.hide();
        $('#workflow_filter').submit();
      });
    }
  });
  $('#workflowsSummary .pager_nav a').click(function(event)
  {
    event.preventDefault();
    $.ajax({
      url: $(this).attr('href')+'/status/<?php echo $form["status"]->getValue() ; ?>',
      success: function(html) {
        $('#workflowsSummary .search_results_content').html(html);
      }
    });
  });
});
</script>
<?php else :?>
  <?php echo __('Nothing here');?>
<?php endif;?>
