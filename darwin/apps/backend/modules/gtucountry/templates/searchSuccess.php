<?php if($form->isValid()):?>
 <?php if(isset($items) && $items->count() != 0 && isset($orderBy) && isset($orderDir) && isset($currentPage) && isset($is_choose)):?>
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
				<th>
					<a class="sort" href="<?php echo url_for($s_url.'&orderby=iso3166'.( ($orderBy=='iso3166' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
					<?php echo __('ISO 3166');?>
					<?php if($orderBy=='ig_type') echo $orderSign ?>
				</th>
				<th>
					<a class="sort" href="<?php echo url_for($s_url.'&orderby=name_en'.( ($orderBy=='name_en' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
					<?php echo __('English name');?>
					<?php if($orderBy=='ig_type') echo $orderSign ?>
				</th>
				<th>					
					<?php echo __('Name 1');?>					
				</th>
				<th>					
					<?php echo __('Name 2');?>					
				</th>
				<th>					
					<?php echo __('Name 3');?>					
				</th>
				<th>					
					<?php echo __('Name 4');?>					
				</th>
				<th>
				</th>
			</thead>
			<tbody>
				<?php foreach($items as $item):?>
					<tr class="rid_<?php echo $item->getId(); ?>">
						<td><?php print($item->getIso3166());?></td>
						<td><?php print($item->getNameEn());?></td>
						<td><?php (strlen($item->getName_1())>0)?print($item->getName_1().' ('.$item->getLangName_1().')'):print("");?></td>
						<td><?php (strlen($item->getName_2())>0)?print($item->getName_2().' ('.$item->getLangName_2().')'):print("");?></td>
						<td><?php (strlen($item->getName_3())>0)?print($item->getName_3().' ('.$item->getLangName_3().')'):print("");?></td>
						<td><?php (strlen($item->getName_4())>0)?print($item->getName_4().' ('.$item->getLangName_4().')'):print("");?></td>
						<td class="<?php echo (! $is_choose)?'edit':'choose';?>">
						<?php if(! $is_choose):?>
						  <?php if ($sf_user->isAtLeast(Users::MANAGER)) : ?>
							<?php echo link_to(image_tag('edit.png',array('title'=>'Edit IGS')),'gtucountry/edit?id='.$item->getId(), array("target"=> "_blank"));?>							
						  <?php endif ;?>
						  <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'gtucountry/view?id='.$item->getId(), array("target"=> "_blank"));?>
						<?php else:?>
						  <div class="result_choose"><?php echo __('Choose');?></div>
						<?php endif;?>
				  </td>
					</tr>
				<?php endforeach; ?>
			</tbody>
	   </table>
    </div>
	<?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
  <?php else:?>
    <?php echo __('No Country Matching');?>
  <?php endif;?>
<?php else:?>
  <div class="error">
    
      <?php echo $form->renderGlobalErrors();?>
    
    
  </div>
<?php endif;?>
