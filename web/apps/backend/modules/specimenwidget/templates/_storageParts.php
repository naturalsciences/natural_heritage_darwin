<table class="storage_place"  id="spec_storage_place">
    <thead style="<?php echo ($form['StorageParts']->count() || $form['newStorageParts']->count())?'':'display: none;';?>" class="spec_storage_parts_head">
    <tr>   
      <th><?php echo __('Storage parts');?></th>
      <th><?php echo $form['StorageParts_holder'];?></th>
    </tr>
  </thead>
  <?php $retainedKey = 0;?>
  <?php foreach($form['StorageParts'] as $form_value):?>
     <?php include_partial('specimen/spec_storage_parts', array('form' => $form_value, 'rownum'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
  <?php endforeach;?>
  <?php foreach($form['newStorageParts'] as $form_value):?>
     <?php include_partial('specimen/spec_storage_parts', array('form' => $form_value, 'rownum'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
  <?php endforeach;?>          
   <tfoot>
     <tr>
       <td colspan="3">
         <div class="add_storage_parts">
          <?php if($module == 'specimen') $url = 'specimen/addStorageParts';
          
          ?>
           <a href="<?php echo url_for($url.($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>/num/" id="add_storage_parts"><?php echo __('Add storage place');?></a>
         </div>
       </td>
     </tr>
   </tfoot>
</table>

<script  type="text/javascript">
$(document).ready(function () {

    $('#add_storage_parts').click( function()
    {
        hideForRefresh('#storage_place');
        parent_el = $(this).closest('table.storage_place');
        parentId = $(parent_el).attr('id');
        $.ajax(
        {
          type: "GET",
          url: $(this).attr('href')+ ($('table#'+parentId+' tbody.spec_storage_parts_data').length),
          success: function(html)
          {                    
            $(parent_el).append(html);
            showAfterRefresh('#storage_place');
          }
        });
        $(this).closest('table.storage_place').find('thead').show();
        return false;
    }); 
    //one storage place is required
    <?php if ((sfContext::getInstance()->getActionName()=="new")&&(sfContext::getInstance()->getRequest()->hasParameter("split_id")===FALSE)&&(sfContext::getInstance()->getRequest()->hasParameter("duplicate_id")===FALSE)): ?>
        $('#add_storage_parts').click( );
    <?php endif;?>
    
});
</script>