<?php if($form->isValid()):?>
  <?php if(isset($expeditions) && $expeditions->count() != 0 && isset($orderBy) && isset($orderDir) && isset($currentPage)):?>
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
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=ig_num'.( ($orderBy=='ig_num' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('I.G. number:');?>
                <?php if($orderBy=='ig_num') echo $orderSign ?>
              </a>
            </th>
            <th>&nbsp;</th>
            <th class="expedition_name">
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=expedition_name'.( ($orderBy=='expedition_name' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('Expeditions');?>
                <?php if($orderBy=='expedition_name') echo $orderSign ?>
              </a>
            </th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>      
          <?php foreach($expeditions as $expedition):?>
            <tr class="rid_<?php echo $expedition->getIgNum(); ?>">
              <td><?php echo $expedition->getIgNum();?></td>
              <td class="edit>">
                <?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
                  <?php echo link_to(image_tag('edit.png',array('title'=>'Edit I.G.')),'igs/edit?id='.$expedition->getIgRef());?>
                  <?php echo link_to(image_tag('duplicate.png',array('title'=>'Duplicate I.G.')),'igs/new?duplicate_id='.$expedition->getIgRef());?>
                <?php endif ; ?>
                <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'igs/view?id='.$expedition->getIgRef());?>               
              </td>
              <td class="datesNum"><?php echo $expedition->getExpeditionName();?></td>
              <td class="edit>">
                <?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
                  <?php echo link_to(image_tag('edit.png',array('title'=>'Edit expedition')),'expedition/edit?id='.$expedition->getExpeditionRef());?>
                  <?php echo link_to(image_tag('duplicate.png',array('title'=>'Duplicate expedition')),'expedition/new?duplicate_id='.$expedition->getExpeditionRef());?>
                <?php endif ; ?>
                <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'expedition/view?id='.$expedition->getExpeditionRef());?>               
              </td>
            </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
    <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
  <?php else:?>
    <?php echo __('No Expedition or I.G. Matching');?>
  <?php endif;?>
<?php else:?>
  <div class="error">
    <?php echo $form['ig_num']->renderError() ?>
    <?php echo $form['expedition_name']->renderError() ?>
</div>
<?php endif;?>
