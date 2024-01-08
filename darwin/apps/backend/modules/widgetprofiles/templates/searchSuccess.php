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
		  <th><a class="sort" href="<?php echo url_for($s_url.'&orderby=name'.( ($orderBy=='name' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Name');?>
              <?php if($orderBy=='name') echo $orderSign ?>
		 </a>
		</th>
		<th><a class="sort" href="<?php echo url_for($s_url.'&orderby=formated_name'.( ($orderBy=='formated_name' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Creator');?>
              <?php if($orderBy=='formated_name') echo $orderSign ?>
		 </a>
		</th>
         <th><a class="sort" href="<?php echo url_for($s_url.'&orderby=creation_date'.( ($orderBy=='creation_date' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Creation date');?>
              <?php if($orderBy=='creation_date') echo $orderSign ?>
		 </a>
		</th>
		  <th>
		  </th>
        <tr>
      </thead>
      <tbody>
        <?php foreach($items as $item):?>
          <tr class="rid_<?php echo $item->getId();?>">
			 <td><?php echo $item->getId();?></td>
            <td class="item_name"><?php echo $item->getName();?></td>
			<td><?php echo $item->getFormatedName() ?></td>
			<td><?php echo $item->getCreationDate() ?></td>
            <td class="<?php echo (! $is_choose)?'edit':'choose';?>">
              <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'widgetprofiles/view?id='.$item->getId(),array('target'=>"_blank"));?>  <!-- -->
                <?php if(! $is_choose):?>                  
                  <?php if ($sf_user->isAtLeast(Users::MANAGER)) : ?>
	                  <?php echo link_to(image_tag('edit.png',array('title'=>'Edit Profile')),'widgetprofiles/edit?id='.$item->getId());?>
	                  <?php echo link_to(image_tag('duplicate.png',array('title'=>'Duplicate Profile')),'widgetprofiles/new?duplicate_id='.$item->getId());?>
	                <?php endif ; ?>
                <?php else:?>
                  <?php if ($sf_user->isAtLeast(Users::MANAGER)) : ?>
                    <?php echo link_to(image_tag('edit.png',array('title'=>'Edit profile')),'widgetprofiles/edit?id='.$item->getId(),array('target'=>"_blank"));?>
                    <?php echo link_to(image_tag('duplicate.png',array('title'=>'Duplicate Profile')),'widgetprofiles/new?duplicate_id='.$item->getId(),array('target'=>"_blank"));?>
                  <?php endif ; ?>                
                    <div class="result_choose"><?php echo __('Choose');?></div>
                <?php endif;?>
            </td>
          </tr>
          <tr class="hidden details details_rid_<?php echo $item->getId();?>" >
            <td colspan="8"></td>
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
<script>
  $("img.info").click(function() {
      item_row=$(this).closest('tr');
      el_id  = getIdInClasses(item_row);
      if($('.details_rid_'+el_id).is(":hidden"))
      {
	if($('.details_rid_'+el_id+' > td:first ').html() == '')
	{
	  $.get('<?php echo url_for('people/details');?>/id/'+el_id,function (html){
	    $('.details_rid_'+el_id+' > td:first ').html(html).parent().show();
	  });
	}
	else
	{
	  $('.details_rid_'+el_id+'').show();
	}
      }
      else
	$('.details_rid_'+el_id+'').hide();
  });
</script>
