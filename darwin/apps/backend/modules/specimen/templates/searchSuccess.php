<?php if($form->isValid()):?>
  <?php if(isset($specimens) && $specimens->count() != 0 && isset($orderBy) && isset($orderDir) && isset($currentPage) && isset($is_choose)):?>
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
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=collection_name'.( ($orderBy=='collection_name' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('Collection');?>
                <?php if($orderBy=='collection_name') echo $orderSign ?>
              </a>
            </th>
            <th>
              <?php echo __('Code(s)');?>
            </th>
            <th>
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=taxon_name'.( ($orderBy=='taxon_name' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('Taxon');?>
                <?php if($orderBy=='taxon_name') echo $orderSign ?>
              </a>
            </th>
            <th>
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=taxon_level_ref'.( ($orderBy=='taxon_level_ref' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('Level');?>
                <?php if($orderBy=='taxon_level_ref') echo $orderSign ?>
              </a>
            </th>
            <th>
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=ig_num'.( ($orderBy=='ig_num' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('I.G. num');?>
                <?php if($orderBy=='ig_num') echo $orderSign ?>
              </a>
            </th>            
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($specimens as $specimen):?>
            <tr class="rid_<?php echo $specimen->getId(); ?>">
              <td class="top_aligned"><?php echo $specimen->getCollectionName();?></td>
              <td>
                <ul>
                  <?php if (!isset($codes[$specimen->getId()])): ?>
                    <li>
                      <?php echo '-';?>
                    </li>
                  <?php else:?>
                    <?php foreach($codes[$specimen->getId()] as $code):?>
                      <li style="font-weight:<?php echo ($code->getCodeCategory()=='main')?'bold':'normal';?>">
                            <?php echo $code->getCodeFormated(); ?>
                      </li>
                    <?php endforeach;?>
                  <?php endif;?>
                </ul>
              </td>
              <td class="top_aligned"><?php echo $specimen->getTaxonName();?></td>
              <td class="top_aligned"><?php echo $specimen->getTaxonLevelName();?></td>
              <td class="top_aligned"><?php echo $specimen->getIgNum();?></td>              
              <td class="choose top_aligned">
                <div class="result_choose"><?php echo __('Choose');?></div>
              </td>
            </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
    <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
  <?php else:?>
    <?php echo __('No Specimen Matching');?>
  <?php endif;?>
<?php else:?>
  <div class="error">
    <?php echo $form['taxon_name']->renderError() ?>
</div>
<?php endif;?>
