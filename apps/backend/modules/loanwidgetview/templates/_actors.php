<table id="sender_table" class="catalogue_table_view">
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
   <?php foreach($senders as $actor):?>
      <td colspan="4">  <?php echo image_tag($people_ids[$actor->getPeopleRef()]->getCorrespondingImage()) ; ?> <?php echo $people_ids[$actor->getPeopleRef()]->getFormatedName(); ?></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Responsible') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Contact') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Checker') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Preparator') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Accompanist') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Transporter') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Other') ? 'on':'off'; ?>" /></td>
   <?php endforeach;?>
 </tbody>
</table>

<br /><br />
<table id="receiver_table" class="catalogue_table_view">
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
   <?php foreach($receivers as $actor):?>
      <td colspan="4">  <?php echo image_tag($people_ids[$actor->getPeopleRef()]->getCorrespondingImage()) ; ?> <?php echo $people_ids[$actor->getPeopleRef()]->getFormatedName(); ?></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Responsible') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Contact') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Checker') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Preparator') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Accompanist') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Transporter') ? 'on':'off'; ?>" /></td>
      <td><span class="spr_checkbox_<?php echo $actor->getIsARole('Other') ? 'on':'off'; ?>" /></td>
   <?php endforeach;?>
 </tbody> 
</table>
