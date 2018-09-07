<table id="user_table" class="catalogue_table_view">
  <thead>
    <tr>
      <th><?php echo __("Name") ; ?></th>
      <th><?php echo __("Edition right") ; ?></th>
    </tr>
  </thead>
 <tbody id="user_body">
   <?php foreach($users_rights as $usr):?>
    <tr>
      <td><?php echo image_tag($users_ids[$usr->getUserRef()]->getCorrespondingImage()) ; ?> <?php echo $users_ids[$usr->getUserRef()]->getFormatedName(); ?></td>
      <td><span class="spr_checkbox_<?php echo $usr->getHasEncodingRight() ? 'on':'off'; ?>" /></td>
    </tr>
   <?php endforeach;?>
 </tbody> 
</table>
