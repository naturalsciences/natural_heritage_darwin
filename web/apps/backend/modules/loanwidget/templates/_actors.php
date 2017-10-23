<table id="sender_table" class="catalogue_table edition">
  <thead>
    <tr>
      <th colspan="4"><?php echo __("Sender side") ; ?></th>
      <th colspan="8"><?php echo __("Roles") ; ?></th>
    </tr>
    <tr>
      <th colspan="4"></th>      
      <th><?php echo __("Responsible") ; ?></th>
      <th><?php echo __("Contact") ; ?></th>
      <th><?php echo __("Checker") ; ?></th>
      <th><?php echo __("Preparator") ; ?></th>
      <th><?php echo __("Attendant") ; ?></th>
      <th><?php echo __("Transporter") ; ?></th>
      <th><?php echo __("Other") ; ?></th>
      <th><?php echo $form['sender'];?></th>
    </tr>
  </thead>
 <tbody id="sender_body">
   <?php $retainedKey = 0;?>
   <?php foreach($form['ActorsSender'] as $form_value):?>   
     <?php include_partial('loan/actors_association', array('type' => 'sender','form' => $form_value, 'row_num'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
   <?php endforeach;?>
   <?php foreach($form['newActorsSender'] as $form_value):?>
     <?php include_partial('loan/actors_association', array('type' => 'sender','form' => $form_value, 'row_num'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
   <?php endforeach;?>
 </tbody>
 <tfoot>
   <tr>
     <td colspan="12">
         <a href="<?php echo url_for('loan/addActors?table='.$table.($form->getObject()->isNew() ? '': '&id='.$form->getObject()->getId()) );?>/type/sender/num/" class="hidden"></a>
         <a class="add_actor" href="<?php echo url_for('people/searchBoth?with_js=1');?>"><?php echo __('Add Sender');?></a>
     </td>
   </tr>
 </tfoot>  
</table>

<table id="receiver_table" class="catalogue_table edition">
  <thead>
    <tr>
      <th colspan="4"><?php echo __("Receiver side") ; ?></th>
      <th colspan="8"><?php echo __("Roles") ; ?></th>
    </tr>
    <tr>
      <th colspan="4"></th>
      <th><?php echo __("Responsible") ; ?></th>
      <th><?php echo __("Contact") ; ?></th>
      <th><?php echo __("Checker") ; ?></th>
      <th><?php echo __("Preparator") ; ?></th>
      <th><?php echo __("Attendant") ; ?></th>
      <th><?php echo __("Transporter") ; ?></th>
      <th><?php echo __("Other") ; ?></th>
      <th><?php echo $form['receiver'];?></th>
    </tr>
  </thead>
 <tbody id="receiver_body">
   <?php $retainedKey = 0;?>
   <?php foreach($form['ActorsReceiver'] as $form_value):?>   
     <?php include_partial('loan/actors_association', array('type' => 'receiver','form' => $form_value, 'row_num'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
   <?php endforeach;?>
   <?php foreach($form['newActorsReceiver'] as $form_value):?>
     <?php include_partial('loan/actors_association', array('type' => 'receiver','form' => $form_value, 'row_num'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
   <?php endforeach;?>
 </tbody> 
 <tfoot>
   <tr>
     <td colspan="12">
         <a href="<?php echo url_for('loan/addActors?table='.$table.($form->getObject()->isNew() ? '': '&id='.$form->getObject()->getId()) );?>/type/receiver/num/" class="hidden"></a>
         <a class="add_actor" href="<?php echo url_for('people/searchBoth?with_js=1');?>"><?php echo __('Add Receiver');?></a>
     </td>
   </tr>
 </tfoot> 
</table>

<script  type="text/javascript">
$(document).ready(function () {


function addSender(people_ref, people_name)
{ 
  info = 'ok';
  $('#sender_body tr').each(function() {
    if($(this).find('input[id$=\"_people_ref\"]').val() == people_ref) info = 'bad' ;
  });
  if(info != 'ok') return false;
  hideForRefresh($('.ui-tooltip-content .page')) ; 
  $.ajax(
  {
    type: "GET",
    url: $('#sender_table a.hidden').attr('href')+ (0+$('#sender_body tr').length)+'/people_ref/'+people_ref + '/order_by/' + (0+$('#sender_table tr').length),
    success: function(html)
    {
      $('#sender_body').append(html);
      $.fn.catalogue_people.reorder($('#sender_table'));
      showAfterRefresh($('.ui-tooltip-content .page')) ; 
    }
  }); 
  return true;
}

function addReceiver(people_ref, people_name)
{ 
  info = 'ok';
  $('#receiver_body tr').each(function() {
    if($(this).find('input[id$=\"_people_ref\"]').val() == people_ref) info = 'bad' ;
  });
  if(info != 'ok') return false;
  hideForRefresh($('.ui-tooltip-content .page')) ; 
  $.ajax(
  {
    type: "GET",
    url: $('#receiver_table a.hidden').attr('href')+ (0+$('#receiver_body tr').length)+'/people_ref/'+people_ref + '/order_by/' + (0+$('#sender_table tr').length),
    success: function(html)
    {
      $('#receiver_body').append(html);
      $.fn.catalogue_people.reorder($('#receiver_table'));
      showAfterRefresh($('.ui-tooltip-content .page')) ; 
    }
  }); 
  return true;
}
$("#sender_table").catalogue_people({handle: '.sender_table_handle', add_button: '#sender_table a.add_actor', q_tip_text: '<?php echo __('Add Sender');?>',update_row_fct: addSender });
$("#receiver_table").catalogue_people({handle: '.receiver_table_handle', add_button: '#receiver_table a.add_actor', q_tip_text: '<?php echo __('Add Receiver');?>',update_row_fct: addReceiver });

});

</script>
