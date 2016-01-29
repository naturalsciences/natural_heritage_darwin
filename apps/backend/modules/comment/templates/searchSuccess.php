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
	    <a class="sort" href="<?php echo url_for($s_url.'&orderby=comment'.( ($orderBy=='comment' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
	      <?php echo __('Comment');?>
	      <?php if($orderBy=='comment') echo $orderSign ?>
	    </a>
          </th>
          <th>
	    <a class="sort" href="<?php echo url_for($s_url.'&orderby=notion_concerned'.( ($orderBy=='notion_concerned' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
	      <?php echo __('Notion concerned');?>
	      <?php if($orderBy=='notion_concerned') echo $orderSign ?>
	    </a>
          </th>
          <th></th>
        <tr>
      </thead>
      <tbody>
        <?php foreach($items as $item):?>
          <tr>
            <td><?php echo image_tag('info.png',"title=info class=info");?></td>
            <td><?php echo $item->getReferencedRelation() ?></td>
            <td><?php echo $item->getComment();?></td>
            <td><?php echo __($item->getNotionText()); ?></td>
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
    <?php echo $form['comment']->renderError(); ?>
    <?php echo $form['notion_concerned']->renderError(); ?>
</div>
<?php endif;?>
