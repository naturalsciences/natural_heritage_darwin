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
  <div class="results_container">
    <table class="results <?php if($is_choose) echo 'is_choose';?>">
      <thead>
          <th>
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=code'.( ($orderBy=='code' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Code');?>
              <?php if($orderBy=='code') echo $orderSign ?>
            </a>
          </th>
          <th><?php echo __('Location');?></th>
          <th class="hidden"></th>
          <th><?php echo __('Latitude');?></th>
          <th><?php echo __('Longitude');?></th>
          <th class="datesNum">
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=gtu_from_date'.( ($orderBy=='gtu_from_date' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('From');?>
                <?php if($orderBy=='gtu_from_date') echo $orderSign ?>
              </a>
          </th>
          <th class="datesNum">
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=gtu_to_date'.( ($orderBy=='gtu_to_date' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('To');?>
                <?php if($orderBy=='gtu_to_date') echo $orderSign ?>
              </a>
          </th>
          <th>
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=elevation'.( ($orderBy=='elevation' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Elevation');?>
              <?php if($orderBy=='elevation') echo $orderSign ?>
            </a>
          </th>
          <th></th>
      </thead>
      <tbody>
        <?php foreach($items as $item):?>
          <tr class="rid_<?php echo $item->getId();?>">
            <td class="top_aligned"><?php echo $item->getCode();?></td>
            <td class=""><?php echo $item->getName(ESC_RAW);?></td>
            <td class="item_name hidden"><?php echo $item->getTagsWithCode(ESC_RAW);?></td>
            <td class=""><?php echo $item->getLatitude();?></td>
            <td class=""><?php echo $item->getLongitude();?></td>
            <td class="datesNum"><?php echo $item->getGtuFromDateMasked(ESC_RAW);?></td>
            <td class="datesNum"><?php echo $item->getGtuToDateMasked(ESC_RAW);?></td>
            <td class=""><?php echo $item->getElevation();?></td>
            <td class="<?php echo (! $is_choose)?'edit':'choose';?> top_aligned">
              <?php if(! $is_choose):?>
                <?php echo link_to(image_tag('edit.png',array('title' => 'Edit')),'gtu/edit?id='.$item->getId());?>
                <?php echo link_to(image_tag('duplicate.png',array('title' => 'Duplicate')),'gtu/new?duplicate_id='.$item->getId());?>
              <?php else:?>
                <div class="result_choose"><?php echo __('Choose');?></div>
                <?php echo link_to(image_tag('edit.png',array('title' => 'Edit')),'gtu/edit?id='.$item->getId(),array('target'=>'_blank'));?>
                <?php echo link_to(image_tag('duplicate.png',array('title' => 'Duplicate')),'gtu/new?duplicate_id='.$item->getId(),array('target'=>'_blank'));?>
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
    <?php echo $form['code']->renderError() ?>
    <?php echo $form['gtu_from_date']->renderError() ?>
    <?php echo $form['gtu_to_date']->renderError() ?>
</div>
<?php endif;?>
