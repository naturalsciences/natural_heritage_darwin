<table id="sender_table" class="catalogue_table_view">
  <thead>
    <tr>
      <th colspan="4"><?php echo __("Actors") .' ('.__("Sender side").')' ; ?></th>
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
    </tr>
  </thead>
 <tbody id="sender_body">
   <?php foreach($senders as $actor):?>
      <tr>
      <td colspan="4"><?php echo image_tag($people_ids[$actor->getPeopleRef()]->getCorrespondingImage()) ; ?> <?php
          $is_physical = $people_ids[$actor->getPeopleRef()]->getIsPhysical();
          echo link_to(
            $people_ids[$actor->getPeopleRef()]->getFormatedName(),
            (($is_physical)?'people':'institution').'/view',
            array('query_string'=>'id='.$actor->getPeopleRef())
          ); ?>
      </td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Responsible') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Contact') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Checker') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Preparator') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Attendant') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Transporter') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Other') ? 'on':'off'; ?>" /></td>
      </tr>
   <?php endforeach;?>
 </tbody>
</table>

<br /><br />
<table id="receiver_table" class="catalogue_table_view">
  <thead>
    <tr>
      <th colspan="4"><?php echo __("Actors") .' ('.__("Receiver side").')' ; ?></th>
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
    </tr>
  </thead>
 <tbody id="receiver_body">
   <?php foreach($receivers as $actor):?>
      <tr>
      <td colspan="4"><?php echo image_tag($people_ids[$actor->getPeopleRef()]->getCorrespondingImage()) ; ?> <?php
          $is_physical = $people_ids[$actor->getPeopleRef()]->getIsPhysical();
          echo link_to(
            $people_ids[$actor->getPeopleRef()]->getFormatedName(),
            (($is_physical)?'people':'institution').'/view',
            array('query_string'=>'id='.$actor->getPeopleRef())
          ); ?></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Responsible') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Contact') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Checker') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Preparator') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo
$actor->getIsARole('Attendant') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Transporter') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Other') ? 'on':'off'; ?>" /></td>
     </tr>
   <?php endforeach;?>
 </tbody>
</table>
