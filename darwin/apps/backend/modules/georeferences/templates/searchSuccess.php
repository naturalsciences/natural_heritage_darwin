<?php if($form->isValid()):?>
<?php if(isset($items) && $items->count() != 0):?>
  <?php
    if($orderDir=='asc')
      $orderSign = '<span class="order_sign_down">&nbsp;&#9660;</span>';
    else
      $orderSign = '<span class="order_sign_up">&nbsp;&#9650;</span>';
  ?>
  <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
  Info
  <?php include_partial('global/pager_info', array('form' => $form, 'pagerLayout' => $pagerLayout)); ?>
  info
  <div class="results_container">
    <table class="results <?php if($is_choose) echo 'is_choose';?>">
      <thead>         
		  <th><?php echo __('id');?></th>
		  <th><?php echo __('Name');?></th>
      </thead>
      <tbody>
        <?php foreach($items as $item):?>
          <tr class="rid_<?php echo $item->getId();?>">
			<td><?php  echo $item->getId(ESC_RAW);?></td>
			<td><?php  echo $item->getName(ESC_RAW);?></td>
			<td id="georeferences_with_code" class="item_name hidden class_georeferences_id_<?php print($item->getId());?>"><?php echo $item->getDescription(ESC_RAW);?>
			
			<td>
			<?php if($is_choose ):?>                             
                <div  name="date_choose" class="result_choose"><?php echo __('Choose Georeference');?></div>                                   
            <?php endif;?> 
			</td>
            <td class="<?php echo (! $is_choose)?'edit':'choose';?> top_aligned">
              <?php if(! $is_choose):?>
                <?php echo link_to(image_tag('blue_eyel.png', array("title" => __('View'))),'georeferences/view?id='.$item->getId(),array('target'=>'_blank'));?>
                <?php echo link_to(image_tag('edit.png',array('title' => 'Edit')),'georeferences/edit?id='.$item->getId(),array('target'=>'_blank'));?>
                <?php echo link_to(image_tag('duplicate.png',array('title' => 'Duplicate')),'georeferences/new?duplicate_id='.$item->getId(),array('target'=>'_blank'));?>
              <?php else:?>                
                <?php echo link_to(image_tag('blue_eyel.png', array("title" => __('View'))),'georeferences/view?id='.$item->getId(),array('target'=>'_blank'));?>
                <?php echo link_to(image_tag('edit.png',array('title' => 'Edit')),'georeferences/edit?id='.$item->getId(),array('target'=>'_blank'));?>
                <?php echo link_to(image_tag('duplicate.png',array('title' => 'Duplicate')),'georeferences/new?duplicate_id='.$item->getId(),array('target'=>'_blank'));?>
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
   ERROR
	<?php $errors = $form->getErrorSchema()->getErrors() ?>
      <?php if($form->hasGlobalErrors()||count($errors)>0):?>
	  List
        <ul class="spec_error_list">
          <?php foreach ($form->getErrorSchema()->getErrors() as $name => $error): ?>
            <li class="error_fld_<?php echo $name;?>"><?php echo __($error) ?></li>
          <?php endforeach; ?>
		  <?php foreach( $errors as $name => $error ) : ?>
		     <li class="error_fld_<?php echo $name;?>"><?php echo $name ?> : <?php echo __($error) ?></li>
		  <?php endforeach ?>
		  <li>(Issue(s) might be caused by a closed widget containing a mandatory field) </li>
        </ul>
      <?php endif;?>

</div>
<?php endif;?>
