<table class="new_maintenance"  id="new_maintenance">
    <thead style="<?php echo ($form['CollectionMaintenance']->count() || $form['newCollectionMaintenance']->count())?'':'display: none;';?>" class="spec_maintenance_head">
    <tr>   
      <th><?php echo __('Maintenance');?></th>
      <th><?php echo $form['CollectionMaintenance_holder'];?></th>
    </tr>
  </thead>
  <?php $retainedKey = 0;?>
  <?php foreach($form['CollectionMaintenance'] as $form_value):?>
  old
     <?php include_partial('specimen/newmaintenance', array('form' => $form_value, 'rownum'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
  <?php endforeach;?>
  <?php foreach($form['newCollectionMaintenance'] as $form_value):?>
  new
     <?php include_partial('specimen/newmaintenance', array('form' => $form_value, 'rownum'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
  <?php endforeach;?>          
   <tfoot>
     <tr>
       <td colspan="3">
         <div class="add_maintenance">
          <?php if($module == 'specimen') $url = 'specimen/attachMaintenance';
          
          ?>
           <a href="<?php echo url_for($url.($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>/num/" id="add_maintenance"><?php echo __('Add maintenance');?></a>
         </div>
       </td>
     </tr>
   </tfoot>
</table>

<script  type="text/javascript">
$(document).ready(function () {

    $('#add_maintenance').click( function()
    {
        hideForRefresh('#new_maintenance');
        parent_el = $(this).closest('table.new_maintenance');
		console.log($(this).attr('href'));
		
        parentId = $(parent_el).attr('id');
		console.log(parentId);
        $.ajax(
        {
          type: "GET",
          url: $(this).attr('href')+ ($('table#'+parentId+' tbody.maintenance_data').length),
          success: function(html)
          {                    
            $(parent_el).append(html);
            showAfterRefresh('#new_maintenance');
          }
        });
        $(this).closest('table.new_maintenance').find('thead').show();
        return false;
    }); 
    //one storage place is required
   
    
});
</script>
  