<table id="sender_table" class="catalogue_table edition">
  <thead>
    <tr>
      <th colspan="4"><?php echo __("Actors(sender side)") ; ?></th>
      <th colspan="8"><?php echo __("Roles") ; ?></th>
    </tr>
    <tr>
      <th colspan="4"></th>
      <th><?php echo __("Responsible") ; ?></th>
      <th><?php echo __("Contact") ; ?></th>
      <th><?php echo __("Checker") ; ?></th>
      <th><?php echo __("Preparator") ; ?></th>
      <th><?php echo __("Accompanist") ; ?></th>
      <th><?php echo __("Transporter") ; ?></th>
      <th><?php echo __("Other") ; ?></th>
    </tr>
  </thead>
 <tbody id="sender_body">
   <?php /*foreach($form['ActorsSender'] as $form_value):?>   
     <?php include_partial('loan/actors_association', array('type' => 'sender','form' => $form_value, 'row_num'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
   <?php endforeach;*/?>
 </tbody>
</table>

<table id="receiver_table" class="catalogue_table edition">
  <thead>
    <tr>
      <th colspan="4"><?php echo __("Actors(receiver side)") ; ?></th>
      <th colspan="8"><?php echo __("Roles") ; ?></th>
    </tr>
    <tr>
      <th colspan="4"></th>
      <th><?php echo __("Responsible") ; ?></th>
      <th><?php echo __("Contact") ; ?></th>
      <th><?php echo __("Checker") ; ?></th>
      <th><?php echo __("Preparator") ; ?></th>
      <th><?php echo __("Accompanist") ; ?></th>
      <th><?php echo __("Transporter") ; ?></th>
      <th><?php echo __("Other") ; ?></th>
    </tr>
  </thead>
 <tbody id="receiver_body">
   <?php /*foreach($form['ActorsReceiver'] as $form_value):?>   
     <?php include_partial('loan/actors_association', array('type' => 'receiver','form' => $form_value, 'row_num'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
   <?php endforeach;*/?>
 </tbody> 
</table>
