<table class="property_values ecology"  id="spec_ident_ecology">
    <thead style="<?php echo ($form['Ecology']->count() || $form['newEcology']->count())?'':'display: none;';?>" class="spec_ident_ecology_head">
    <tr>   
      <th><?php echo __('Ecology');?></th>
      <th><?php echo $form['Ecology_holder'];?></th>
    </tr>
  </thead>
  <?php $retainedKey = 0;?>
  <?php foreach($form['Ecology'] as $form_value):?>
     <?php include_partial('specimen/spec_ecology', array('form' => $form_value, 'rownum'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
  <?php endforeach;?>
  <?php foreach($form['newEcology'] as $form_value):?>
     <?php include_partial('specimen/spec_ecology', array('form' => $form_value, 'rownum'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
  <?php endforeach;?>          
   <tfoot>
     <tr>
       <td colspan="3">
         <div class="add_ecology">
          <?php if($module == 'specimen') $url = 'specimen/addEcology';
          
          ?>
           <a href="<?php echo url_for($url.($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>/num/" id="add_ecology"><?php echo __('Add Ecology');?></a>
         </div>
       </td>
     </tr>
   </tfoot>
</table>

<script  type="text/javascript">
$(document).ready(function () {

    $('#add_ecology').click( function()
    {
        hideForRefresh('#ecology');
        parent_el_eco = $(this).closest('table.ecology');
        parentId = $(parent_el_eco).attr('id');
        $.ajax(
        {
          type: "GET",
          url: $(this).attr('href')+ ($('table#'+parentId+' tbody.spec_ident_ecology_data').length),
          success: function(html)
          {                    
            $(parent_el_eco).append(html);
            showAfterRefresh('#ecology');
          }
        });
        $(this).closest('table.ecology').find('thead').show();
        return false;
    }); 
});
</script>