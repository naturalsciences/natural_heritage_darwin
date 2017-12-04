<?php if($form->isValid()):?>
  <?php if(isset($expeditions) && $expeditions->count() != 0 && isset($orderBy) && isset($orderDir) && isset($currentPage) && isset($is_choose)):?>
    <?php
      if($orderDir=='asc')
        $orderSign = '<span class="order_sign_down">&nbsp;&#9660;</span>';
      else
        $orderSign = '<span class="order_sign_up">&nbsp;&#9650;</span>';
    ?>
    <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
    <?php include_partial('global/pager_info', array('form' => $form, 'pagerLayout' => $pagerLayout)); ?>
    <div class="results_container">
      <table class="results">
        <thead>
          <tr>
            <th>
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=name'.( ($orderBy=='name' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('Name');?>
                <?php if($orderBy=='name') echo $orderSign ?>
              </a>
            </th>
            <th class="datesNum">
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=expedition_from_date'.( ($orderBy=='expedition_from_date' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('From');?>
                <?php if($orderBy=='expedition_from_date') echo $orderSign ?>
              </a>
            </th>
            <th class="datesNum">
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=expedition_to_date'.( ($orderBy=='expedition_to_date' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('To');?>
                <?php if($orderBy=='expedition_to_date') echo $orderSign ?>
              </a>
            </th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($expeditions as $expedition):?>
            <tr class="rid_<?php echo $expedition->getId(); ?>">
              <td><?php echo $expedition->getName();?></td>
              <td class="datesNum"><?php echo $expedition->getExpeditionFromDateMasked(ESC_RAW);?></td>
              <td class="datesNum"><?php echo $expedition->getExpeditionToDateMasked(ESC_RAW);?></td>
                <td class="<?php echo (! $is_choose)?'edit':'choose';?>">
                  <?php if(! $is_choose):?>
                    <?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>                  
                      <?php echo link_to(image_tag('edit.png',array('title'=>'Edit expedition')),'expedition/edit?id='.$expedition->getId());?>
                      <?php echo link_to(image_tag('duplicate.png',array('title'=>'Duplicate expedition')),'expedition/new?duplicate_id='.$expedition->getId());?>
                    <?php endif ; ?>
                    <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'expedition/view?id='.$expedition->getId());?>               
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
    <?php echo __('No Expedition Matching');?>
  <?php endif;?>
<?php else:?>
  <div class="error">
    <?php if(!$form['expedition_to_date']->hasError()): ?>
      <?php echo $form->renderGlobalErrors();?>
    <?php endif; ?>
    <?php echo $form['name']->renderError() ?>
    <?php echo $form['expedition_from_date']->renderError() ?>
    <?php if(!$form['expedition_from_date']->hasError()): ?>
      <?php echo $form['expedition_to_date']->renderError() ?>
    <?php endif; ?>
</div>
<?php endif;?>
