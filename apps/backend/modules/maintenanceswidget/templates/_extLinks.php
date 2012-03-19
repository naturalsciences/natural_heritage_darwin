<table class="property_values extlinks"  id="spec_ident_extlink">
    <thead style="<?php echo ($form['ExtLinks']->count() || $form['newExtLinks']->count())?'':'display: none;';?>" class="spec_ident_extlinks_head">
    <tr>   
      <th><?php echo __('Url');?></th>
      <th><?php echo __('Comment');?></th>
      <th><?php echo $form['extlink'];?></th>
    </tr>
  </thead>
  <?php $retainedKey = 0;?>
  <?php foreach($form['ExtLinks'] as $form_value):?>   
     <?php include_partial('specimen/spec_links', array('form' => $form_value, 'rownum'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
  <?php endforeach;?>
  <?php foreach($form['newExtLinks'] as $form_value):?>
     <?php include_partial('specimen/spec_links', array('form' => $form_value, 'rownum'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
  <?php endforeach;?>          
   <tfoot>
     <tr>
       <td colspan="3">
         <div class="add_comments">
           <a href="<?php echo url_for('maintenances/addExtLinks'.($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>/num/" id="add_links"><?php echo __('Add Url');?></a>
         </div>
       </td>
     </tr>
   </tfoot>
</table>
 
<script  type="text/javascript">
$(document).ready(function () {

    $('#add_links').click( function(event)
    {
        event.preventDefault();
        hideForRefresh('#extLinks');
        parent_el = $(this).closest('table.extlinks');
        parentId = $(parent_el).attr('id');
        $.ajax(
        {
          type: "GET",
          url: $(this).attr('href')+ ($('table#'+parentId+' tbody.spec_ident_extlinks_data').length),
          success: function(html)
          {                    
            $(parent_el).append(html);
            showAfterRefresh('#extLinks');
          }
        });
        $(this).closest('table.extlinks').find('thead').show();
        return false;
    }); 
});
</script>
