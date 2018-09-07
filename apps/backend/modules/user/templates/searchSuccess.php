<?php if($form->isValid()):?>
<?php if(isset($items) && $items->count() != 0):?>
  <?php
    if($orderDir=='asc')
      $orderSign = '<span class="order_sign_down">&nbsp;&#9660;</span>';
    else
      $orderSign = '<span class="order_sign_up">&nbsp;&#9650;</span>';
  ?>
  <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
  <?php include_partial('global/pager_info', array('form' => $form, 'pagerLayout' => $pagerLayout)); ?>
  <?php use_helper('Date');?>
  <div class="results_container">
    <table class="results <?php if($is_choose) echo 'is_choose';?>">
      <thead>
        <tr>
          <th class="hidden"></th>
          <th>
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=is_physical,gender'.( ($orderBy=='is_physical,gender' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Status');?>
              <?php if($orderBy=='is_physical,title') echo $orderSign ?>
            </a>
          </th>
          <th>
	    <a class="sort" href="<?php echo url_for($s_url.'&orderby=family_name'.( ($orderBy=='family_name' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
	      <?php echo __('Name');?>
	      <?php if($orderBy=='family_name') echo $orderSign ?>
	    </a>
          </th>
          <th>
	    <a class="sort" href="<?php echo url_for($s_url.'&orderby=given_name'.( ($orderBy=='given_name' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
	      <?php echo __('Given Name');?>
	      <?php if($orderBy=='given_name') echo $orderSign ?>
	    </a>
          </th>
          <th>
	    <a class="sort" href="<?php echo url_for($s_url.'&orderby=db_user_type'.( ($orderBy=='db_user_type' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
	      <?php echo __('Type');?>
	      <?php if($orderBy=='db_user_type') echo $orderSign ?>
	    </a>
          </th>
         <?php if($sf_user->isAtLeast(Users::ADMIN)):?>
          <th>
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=last_seen'.( ($orderBy=='last_seen' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Last Seen');?>
              <?php if($orderBy=='last_seen') echo $orderSign ?>
            </a>
          </th>
          <?php endif;?>
          <th></th>
        <tr>
      </thead>
      <tbody>
        <?php foreach($items as $item):?>
          <tr class="rid_<?php echo $item->getId();?>">
            <td class="hidden item_name"><?php echo $item->getFormatedName();?></td>
            <td><img src="/images/user_suit_<?php echo $item->getStatus() ?>.png" label="<?php echo $item->getTitle() ?>"></td>
            <td><?php echo $item->getFamilyName();?></td>
            <td><?php echo $item->getGivenName();?></td>
            <td><?php echo $item->getTypeName($item->getDbUserType()) ?></td>
            <?php if($sf_user->isAtLeast(Users::ADMIN)):?>
              <td><?php echo format_datetime($item->getLastSeen(),'f'); ?></td>
            <?php endif;?>
            <td class="<?php echo (! $is_choose)?'edit':'choose';?>">
                <?php if(!$is_choose):?>
                  <?php if($sf_user->isA(Users::ADMIN) || ($item->getDbUserType() < $sf_user->getDbUserType())) : ?>
                    <?php echo link_to(image_tag('edit.png'),'user/edit?id='.$item->getId());?>
                  <?php endif ; ?>
                <?php else:?>
                  <div class="result_choose"><?php echo __('Choose');?></div>
                <?php endif;?>
            </td>
          </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
  <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
<?php else:?>
  <?php echo __('No Matching Items');?>
<?php endif;?>

<?php else:?>
<div class="error">

    <?php echo $form->renderGlobalErrors();?>
    <?php echo $form['family_name']->renderError(); ?>
</div>
<?php endif;?>
