<?php if(isset($notion)):?>
  <?php if($form->isValid()):?>
    <?php if(isset($methods_and_tools) 
            && $methods_and_tools->count() != 0 
            && isset($orderBy) 
            && isset($orderDir) 
            && isset($currentPage) 
            && isset($is_choose)
            ):?>
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
                <a class="sort" href="<?php echo url_for($s_url.'&orderby='.$notion.( ($orderBy==$notion && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                  <?php echo __('Name');?>
                  <?php if($orderBy==$notion) echo $orderSign ?>
                </a>
              </th>
              <th>&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($methods_and_tools as $method_and_tool):?>
              <tr class="rid_<?php echo $method_and_tool->getId(); ?>">
                <td><?php echo $method_and_tool->getName();?></td>
                <?php if ($sf_user->isAtleast(Users::ENCODER)) : ?>                
                  <td class="<?php echo (! $is_choose)?'edit':'choose';?>">
                    <?php if(! $is_choose):?>
                      <?php echo link_to(image_tag('edit.png',array('title'=>'Edit '.$notion)),'methods_and_tools/edit?id='.$method_and_tool->getId().'&notion='.$notion);?>
                      <?php echo link_to(image_tag('duplicate.png',array('title'=>'Duplicate '.$notion)),'methods_and_tools/new?duplicate_id='.$method_and_tool->getId().'&notion='.$notion);?>
                    <?php else:?>
                      <div class="result_choose"><?php echo __('Choose');?></div>
                    <?php endif;?>
                  </td>
                <?php else : ?>
                  <td>
                    &nbsp;
                  </td>
                <?php endif ; ?>                  
              </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      </div>
      <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
    <?php else:?>
      <?php echo __('No '.$notion.' matching');?>
    <?php endif;?>
  <?php else:?>
    <div class="error">
      <?php echo $form[$notion]->renderError() ?>
    </div>
  <?php endif;?>
<?php else:?>
  <div class="error">
    <?php echo __('You need to tell if you wish to work on tools or methods'); ?>
  </div>
<?php endif;?>
