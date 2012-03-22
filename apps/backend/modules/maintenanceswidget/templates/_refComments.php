<table class="property_values comments"  id="spec_ident_comments">
    <thead style="<?php echo ($form['Comments']->count() || $form['newComments']->count())?'':'display: none;';?>" class="spec_ident_comments_head">
    <tr>   
      <th><?php echo __('Notion');?></th>
      <th><?php echo __('Comment');?></th>
      <th><?php echo $form['comment'];?></th>
    </tr>
  </thead>
  <?php $retainedKey = 0;?>
  <?php foreach($form['Comments'] as $form_value):?>   
     <?php include_partial('specimen/spec_comments', array('form' => $form_value, 'rownum'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
  <?php endforeach;?>
  <?php foreach($form['newComments'] as $form_value):?>
     <?php include_partial('specimen/spec_comments', array('form' => $form_value, 'rownum'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
  <?php endforeach;?>          
   <tfoot>
     <tr>
       <td colspan="3">
         <div class="add_comments">
           <a href="<?php echo url_for('maintenances/addComments'.($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>/num/" id="add_comment"><?php echo __('Add comment');?></a>
         </div>
       </td>
     </tr>
   </tfoot>
</table>
 
<script  type="text/javascript">
$(document).ready(function () {

  $('#add_comment').click( function()
  {
      hideForRefresh('#refComment');
      parent_el = $(this).closest('table.comments');
      parentId = $(parent_el).attr('id');
      $.ajax(
      {
        type: "GET",
        url: $(this).attr('href')+ ($('table#'+parentId+' tbody.spec_ident_comments_data').length),
        success: function(html)
        {                    
          $(parent_el).append(html);
          showAfterRefresh('#refComment');
        }
      });
      $(this).closest('table.comments').find('thead').show();
      return false;
  }); 
});
</script>
