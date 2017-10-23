<table class="catalogue_table">
  <thead>
    <tr>
      <th><?php echo __('Login Type');?></th>
      <th><?php echo __('User name');?></th>
      <th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($login_info as $login_info):?>
  <tr>
    <td>
      <?php if($login_info->getLoginType() == 'local'):?>
        <a class="link_catalogue" title="<?php echo __('Edit Login');?>"  href="<?php echo url_for('user/loginInfo?id='.$login_info->getId().'&user_ref='.$login_info->getUserRef());?>"">
          <?php echo $login_info->getLoginType();?>
        </a>
      <?php else:?>
        <?php echo $login_info->getLoginType();?>
      <?php endif;?>
    </td>
    <td>
       <?php echo($login_info->getUserName());?>
    </td>
    <td class="widget_row_delete">
    <?php if($sf_user->isAtLeast(Users::MANAGER)) : ?>
      <a id='edit_info' class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=users_login_infos&id='.$login_info->getId());?>" title="<?php echo __('Are you sure ?') ?>"><?php echo image_tag('remove.png'); ?>
      </a>
    <?php endif ; ?>
    </td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>
<br />
<?php if($sf_user->isAtLeast(Users::MANAGER)) : ?>
<?php echo image_tag('add_green.png');?>
<a id='add_info' title="<?php echo __('Add Login');?>" class="link_catalogue" href="<?php echo url_for('user/loginInfo?user_ref='.$eid);?>"> 
<?php echo __('Add');?>
</a>
<?php endif ; ?>

