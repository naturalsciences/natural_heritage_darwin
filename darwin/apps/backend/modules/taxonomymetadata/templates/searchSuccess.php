<?php if($form->isValid()):?>
<?php if(isset($taxonomymetadata_records) && $taxonomymetadata_records->count() != 0):?>
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
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=taxonomy_name'.( ($orderBy=='taxonomy_name' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Taxonomy name');?>
              <?php if($orderBy=='taxonomy_name') echo $orderSign ?>
            </a>
          </th>
          <th>
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=is_reference_taxonomy'.( ($orderBy=='is_reference_taxonomy' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('is reference taxonomy');?>
              <?php if($orderBy=='is_reference_taxonomy') echo $orderSign ?>
            </a>
          </th>
          <th>
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=creation_date'.( ($orderBy=='creation_date' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('creation date');?>
              <?php if($orderBy=='creation_date') echo $orderSign ?>
            </a>
          </th>
          <th></th>
      </thead>
      <tbody>
        <?php foreach($taxonomymetadata_records as $item):?>
          <tr class="rid_<? echo $item->getId();?>">
            <td><?php echo $item->getTaxonomyName(); ?></td>
            <td><?php echo ($item->getIsReferenceTaxonomy())? 'true' : 'false'; ?></td>
            <td><?php echo $item->getCreationDateMasked(ESC_RAW); ?></td>
            <td class="<?php echo (! $is_choose)?'edit':'choose';?>">
              <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'taxonomymetadata/view?id='.$item->getId(),array('target'=>"_blank"));?>
              <?php if(! $is_choose):?>
                <?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
                  <?php echo link_to(image_tag('edit.png', array("title" => __("Edit"))),'taxonomymetadata/edit?id='.$item->getId(),array('target'=>"_blank"));?>
                  <?php echo link_to(image_tag('duplicate.png', array("title" => __("Duplicate"))),'taxonomymetadata/new?duplicate_id='.$item->getId(),array('target'=>"_blank"));?>
                <?php endif ; ?>
              <?php else:?>
                <?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
                  <?php echo link_to(image_tag('edit.png', array("title" => __("Edit"))), 'taxonomymetadata/edit?id='.$item->getId(),array('target'=>"_blank"));?>
                  <?php echo link_to(image_tag('duplicate.png', array("title" => __("Duplicate"))),'taxonomymetadata/new?duplicate_id='.$item->getId(),array('target'=>"_blank"));?>
                <?php endif ; ?>
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
    
</div>
<?php endif;?>