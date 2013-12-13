<fieldset ><legend><b><?php echo __('New Status');?></b></legend>
<table class="catalogue_table<?php if($view) echo '_view' ; ?>">
  <thead class="empty">
    <tr>
      <th><?php echo __('Status');?></th>
      <th><?php echo __('Comments');?></th>
    </tr>
    <tr>
      <td><?php echo $form["status"] ; ?></td>
      <td><ul class="comment_error error_list hidden"><li><?php echo __('Error: The comment is missing');?></li></ul>
        <?php echo $form["comment"] ; ?><a title="<?php echo __('Add Workflow');?>" id="add_workflow" href="<?php echo url_for('informativeWorkflow/add?table='.$table.'&id='.$eid); ?>"><?php echo __('Add');?></a></td>
    </tr>
  </thead>
</table>
</fieldset>
<?php if($informativeWorkflow->count() > 0 && $sf_user->isAtLeast(Users::ENCODER)) : ?>
<table class="catalogue_table">
  <thead class="workflow">
    <tr><th colspan=4><?php echo __("Latest Status") ; ?></th></tr>
    <tr>
      <th><?php echo __('Date');?></th>
      <th><?php echo __('Status');?></th>
      <th><?php echo __('Comments');?></th>
      <th><?php echo __('By');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($informativeWorkflow as $info) : ?>
    <tr>
      <td><?php $date = new DateTime($info->getModificationDateTime());
      		echo $date->format('d/m/Y H:i:s'); ?></td>
      <td><?php echo $info->getFormattedStatus();?></td>
      <td><?php echo $info->getComment();?></td>
      <td><?php echo $info->getUserRef()?$info->Users->__toString():$info->getFormatedName() ;?></td>
      <td>
        <?php if($sf_user->isAtLeast(Users::MANAGER)):?>
          <?php echo link_to(image_tag('remove.png'), 'informativeWorkflow/delete?id='.$info->getId(),'class=workflow_delete');?>
        <?php endif;?>
      </td>
    </tr>
    <?php endforeach ; ?>
    <?php if ($informativeWorkflow->count() == 5 ) : ?>
    <tr>
      <td colspan="3">&nbsp;</td>
      <td>
        <a class="link_catalogue" information="true" title="<?php echo __('view all workflows');?>" href="<?php echo url_for('informativeWorkflow/viewAll?table='.$table.'&id='.$eid); ?>">
        <?php echo __('Browse entire history');?></a>
      </td>
    </tr>
    <?php endif ; ?>
  </tbody>
</table>
<?php endif ; ?>

<script type="text/javascript">
$(document).ready(function () 
{
  $('#add_workflow').click(function(event) {   
   event.preventDefault();
   if($('#informative_workflow_status').val() && $('#informative_workflow_comment').val())
   {
     $(this).load($(this).attr('href'),{'status':$('#informative_workflow_status').val(),'comment':$('#informative_workflow_comment').val()}, function(){
       $('body').data('widgets_screen').refreshWidget(event, $(this));
     });
   } else {
    $('.comment_error').removeClass('hidden');
   }
  });

  $('.workflow_delete').click(function(event)
  {
    event.preventDefault();
    var answer = confirm('<?php echo addslashes(__('Are you sure ?'));?>');
    if(answer) {
      el = $(this);
      row = $(this).closest('tr');
      $.get($(this).attr('href'), function() {
        row.hide();
        $('body').data('widgets_screen').refreshWidget(event, el);
      });
    }
  });

});
</script>
