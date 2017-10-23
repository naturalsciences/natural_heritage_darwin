<table id="user_table" class="catalogue_table edition">
  <thead>
    <tr>
      <th></th>
      <th><?php echo __("Name") ; ?></th>
      <th><?php echo __("Edition right") ; ?></th>
      <th><?php echo $form['users'];?></th>
    </tr>
  </thead>
 <tbody id="user_body">
   <?php $retainedKey = 0;?>
   <?php foreach($form['Users'] as $form_value):?>
     <?php include_partial('loan/darwin_user', array('form' => $form_value, 'row_num'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
   <?php endforeach;?>
   <?php foreach($form['newUsers'] as $form_value):?>
     <?php include_partial('loan/darwin_user', array('form' => $form_value, 'row_num'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
   <?php endforeach;?>
 </tbody>
 <tfoot>
   <tr>
     <td colspan="4">
      <div class="add_code">
         <a href="<?php echo url_for('loan/addUsers'.($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>/num/" class="hidden"></a>
         <a class="add_user" href="<?php echo url_for('user/choose');?>"><?php echo __('Add');?></a>
       </div>
     </td>
   </tr>
 </tfoot>
</table>

<script  type="text/javascript">
$(document).ready(function () {


function addUser(user_ref, user_name)
{
  info = 'ok';
  $('#user_body tr').each(function() {
    if($(this).find('input[id$=\"_user_ref\"]').val() == user_ref) info = 'bad' ;
  });
  if(info != 'ok') return false;
  hideForRefresh($('.ui-tooltip-content .page')) ;
  $.ajax(
  {
    type: "GET",
    url: $('#user_table a.hidden').attr('href')+ (0+$('#user_table tr').length)+'/user_ref/'+user_ref,
    success: function(html)
    {
      $('#user_body').append(html);
      showAfterRefresh($('.ui-tooltip-content .page')) ;
    }
  });
  return true;
}
$("#user_table").catalogue_people({add_button: '#user_table a.add_user', q_tip_text: '<?php echo __('Choose a User');?>',update_row_fct: addUser });
});

</script>
