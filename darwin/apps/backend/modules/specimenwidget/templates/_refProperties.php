<?php if($eid) : ?>
<?php echo get_component('cataloguewidget', 'properties', array('table' => 'specimens', 'eid' => $eid));?>
<?php else : ?>
<table class="new_properties"  id="new_properties">
    <thead style="<?php echo ($form['Properties']->count() || $form['newProperties']->count())?'':'display: none;';?>" class="spec_properties_head">
    <tr>   
      <th><?php echo __('Properties');?></th>
      <th><?php echo $form['Properties_holder'];?></th>
    </tr>
  </thead>
  <?php $retainedKey = 0;?>
  <?php foreach($form['Properties'] as $form_value):?>
     <?php include_partial('specimen/newproperties', array('form' => $form_value, 'rownum'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
  <?php endforeach;?>
  <?php foreach($form['newProperties'] as $form_value):?>
     <?php include_partial('specimen/newproperties', array('form' => $form_value, 'rownum'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
  <?php endforeach;?>          
   <tfoot>
     <tr>
       <td colspan="3">
         <div class="add_storage_parts">
          <?php if($module == 'specimen') $url = 'specimen/attachProperties';
          
          ?>
           <a href="<?php echo url_for($url.($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>/num/" id="add_properties"><?php echo __('Add property');?></a>
         </div>
       </td>
     </tr>
   </tfoot>
</table>

<script  type="text/javascript">
$(document).ready(function () {

    $('#add_properties').click( function()
    {
        hideForRefresh('#new_properties');
        parent_el = $(this).closest('table.new_properties');
		console.log($(this).attr('href'));
		
        parentId = $(parent_el).attr('id');
		console.log(parentId);
        $.ajax(
        {
          type: "GET",
          url: $(this).attr('href')+ ($('table#'+parentId+' tbody.spec_properties_data').length),
          success: function(html)
          {                    
            $(parent_el).append(html);
            showAfterRefresh('#new_properties');
          }
        });
        $(this).closest('table.new_properties').find('thead').show();
        return false;
    }); 
    //one storage place is required
   
    
});
</script>
<?php endif; ?>