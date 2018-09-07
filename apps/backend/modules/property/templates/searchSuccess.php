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
          <th>
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=referenced_relation'.( ($orderBy=='referenced_relation' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Relation');?>
              <?php if($orderBy=='referenced_relation') echo $orderSign ?>
            </a>
          </th>
          <th>
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=property_type'.( ($orderBy=='property_type' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Property type');?>
              <?php if($orderBy=='property_type') echo $orderSign ?>
            </a>
          </th>
          <th>
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=applies_to'.( ($orderBy=='applies_to' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Applies to');?>
              <?php if($orderBy=='applies_to') echo $orderSign ?>
            </a>
          </th>
          <th>
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=lower_value'.( ($orderBy=='lower_value' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Value');?>
              <?php if($orderBy=='lower_value') echo $orderSign ?>
            </a>
          </th>
          <th>
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=property_unit'.( ($orderBy=='property_unit' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Property unit');?>
              <?php if($orderBy=='property_unit') echo $orderSign ?>
            </a>
          </th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($items as $item):?>
          <tr>
            <td><?php echo $item->getReferencedRelation() ?></td>
            <td><?php echo $item->getPropertyType();?></td>
            <td><?php echo $item->getAppliesTo() ?></td>
            <td>
              <?php echo $item->getLowerValue();?>
              <?php if($item->getUpperValue() != ''):?>
                -> <?php echo $item->getUpperValue();?>
              <?php endif;?>
              <?php if($item->getPropertyAccuracy() != ''):?>
                ( +- <?php echo $item->getPropertyAccuracy();?> <?php echo $item->getPropertyUnit();?>)
              <?php endif;?>
            </td>
            <td><?php echo $item->getpropertyUnit() ?></td>
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
    <?php echo $form['property_type']->renderError(); ?>
    <?php echo $form['applies_to']->renderError(); ?>
    <?php echo $form['lower_value']->renderError(); ?>
    <?php echo $form['upper_value']->renderError(); ?>
    <?php echo $form['property_unit']->renderError(); ?>

</div>
<?php endif;?>
