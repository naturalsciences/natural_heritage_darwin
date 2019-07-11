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
        <tr>
          <th></th>
          <th>
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=referenced_relation'.( ($orderBy=='referenced_relation' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Relation');?>
              <?php if($orderBy=='referenced_relation') echo $orderSign ?>
            </a>
          </th>
          <th>
      <a class="sort" href="<?php echo url_for($s_url.'&orderby=type'.( ($orderBy=='type' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
        <?php echo __('Type');?>
        <?php if($orderBy=='type') echo $orderSign ?>
      </a>
          </th>
          <th>
      <a class="sort" href="<?php echo url_for($s_url.'&orderby=title'.( ($orderBy=='title' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
        <?php echo __('Title');?>
        <?php if($orderBy=='title') echo $orderSign ?>
      </a>
          </th>
          <th></th>
        <tr>
      </thead>
      <tbody>
        <?php foreach($items as $item):?>
          <tr>
            <td>
              <?php $alt=($item->getDescription()!='')?$item->getTitle().' / '.$item->getDescription():$item->getTitle();?>
              <?php if($item->hasPreview()):?>
                <a href="<?php echo url_for('multimedia/downloadFile?id='.$item->getId());?>" alt="<?php echo $alt;?>" title="<?php echo $alt;?>"><img src="<?php echo url_for('multimedia/preview?id='.$item->getId());?>" alt="<?php echo $alt;?>" width="50" /></a>
              <?php else:?>
                <?php echo link_to($item->getFileName()." ".image_tag('criteria.png'),'multimedia/downloadFile?id='.$item->getId(), array('alt'=>$alt, 'title'=>$alt)) ; ?>
              <?php endif;?>
            </td>
            <td><?php echo $item->getReferencedRelation() ?></td>
            <td><?php echo $item->getType();?></td>
            <td><?php echo __($item->getTitle()); ?></td>
            <td class="edit">
              <?php if($item->getLink() != ''):?>
                <?php echo link_to(image_tag('next.png', array("title" => __("View"))),$item->getLink());?>
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
    <?php echo $form['referenced_relation']->renderError(); ?>
    <?php echo $form['type']->renderError(); ?>
    <?php echo $form['title']->renderError(); ?>
</div>
<?php endif;?>
