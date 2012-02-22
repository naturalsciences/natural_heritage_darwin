<?php if($eid) : ?>
<fieldset ><legend><b><?php echo __('New Status');?></b></legend>
<table class="catalogue_table">
  <thead class="empty">
    <tr>
      <th><?php echo __('Status');?></th>
      <th><?php echo __('Comments');?></th>
    </tr>
    <tr>
      <td><?php echo $form["status"] ; ?></td>
      <td><?php echo $form["comment"] ; ?><a title="<?php echo __('Add');?>" id="add_status" href="<?php echo url_for('loan/addStatus?id='.$eid); ?>"><?php echo __('Add');?></a></td>
    </tr>    
  </thead>
</table>
</fieldset>
<?php if($loanstatus->count() > 0) : ?>
<table class="catalogue_table">
  <thead class="workflow">
    <tr><th colspan=4><?php echo __("Latest Status") ; ?></th></tr>
    <tr>
      <th><?php echo __('Date');?></th>
      <th><?php echo __('Status');?></th>
      <th><?php echo __('Comments');?></th>
      <th><?php echo __('By');?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($loanstatus as $info) : ?>
    <tr>
      <td><?php $date = new DateTime($info->getModificationDateTime());
      		echo $date->format('Y/m/d H:i:s'); ?></td>
      <td><?php echo $info->getFormattedStatus();?></td>
      <td><?php echo $info->getComment();?></td>
      <td><?php echo $info->Users->__toString() ;?></td>      
    </tr>
    <?php endforeach ; ?>
    <?php if ($loanstatus->count() == 5 ) : ?>
    <tr>
      <td colspan="3">&nbsp;</td>
      <td>
        <a class="link_catalogue" information="true" title="<?php echo __('view all workflows');?>" href="<?php echo url_for('informativeWorkflow/viewAll?table=loans&id='.$eid); ?>">
        <?php echo __('History');?></a>
      </td>
    </tr>   
    <?php endif ; ?>   
  </tbody>
</table>
<?php endif ; ?>

<script type="text/javascript">
$(document).ready(function () 
{
  
  $('#add_status').click(function(event) {   
   event.preventDefault();
   if($('#informative_workflow_status').val() && $('#informative_workflow_comment').val())
   {
     $(this).load($(this).attr('href'),{'status':$('#informative_workflow_status').val(),'comment':$('#informative_workflow_comment').val()}, function(){
       $('body').data('widgets_screen').refreshWidget(event, $(this));
     });
   }
  });
});
</script>
<?php else : ?>
<?php echo __('Please save your loan in order to add status') ?>
<?php endif; ?>
