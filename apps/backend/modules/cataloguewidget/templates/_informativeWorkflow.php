<table class="catalogue_table">
  <tbody>
    <tr><th colspan=2><?php echo __("New Status") ; ?></th></tr>
    <tr>
      <th><?php echo __('Status');?></th>
      <th><?php echo __('Comments');?></th>
    </tr>
    <tr>
      <td><?php echo $form["status"] ; ?></td>
      <td><?php echo $form["comment"] ; ?><a title="<?php echo __('Add Workflow');?>" id="add_workflow" href="<?php echo url_for('informativeWorkflow/add?table='.$table.'&id='.$eid); ?>"><?php echo __('Add');?></a></td>
    </tr>    
  </tbody>
</table>
<?php if($informativeWorkflow) : ?>
<table class="catalogue_table">
  <thead>
    <tr><th colspan=4><?php echo __("Latest Status") ; ?></th></tr>
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
      		echo $date->format('Y/m/d H:i:s'); ?></th>
      <th><?php echo $info->getStatus();?></th>
      <th><?php echo $info->getComment();?></th>
      <th><?php echo $info->Users->__toString();?></th>      
    </tr>
    <?php endforeach ; ?>
  </tbody>
</table>
<?php endif ; ?>

<script type="text/javascript">
$(document).ready(function () 
{
  $('#add_workflow').click(function() {   
   event.preventDefault();
   if($('#informative_workflow_status').val() && $('#informative_workflow_comment').val())
   {
     $(this).load($(this).attr('href'),{'status':$('#informative_workflow_status').val(),'comment':$('#informative_workflow_comment').val()})
     $('body').data('widgets_screen').refreshWidget(event, $(this)) ;
   }
  });
});
</script>
