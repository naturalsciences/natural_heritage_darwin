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
          <th class="hidden"></th>
          <th>
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=title'.( ($orderBy=='title' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Title');?>
              <?php if($orderBy=='title') echo $orderSign ?>
            </a>
          </th>
          <th>
	    <a class="sort" href="<?php echo url_for($s_url.'&orderby=family_name'.( ($orderBy=='family_name' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
	      <?php echo __('Family name');?>
	      <?php if($orderBy=='family_name') echo $orderSign ?>
	    </a>
          </th>
          <th>
	    <a class="sort" href="<?php echo url_for($s_url.'&orderby=given_name'.( ($orderBy=='given_name' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
	      <?php echo __('Given Name');?>
	      <?php if($orderBy=='given_name') echo $orderSign ?>
	    </a>
          </th>
          <th>
	    <a class="sort" href="<?php echo url_for($s_url.'&orderby=additional_names'.( ($orderBy=='additional_names' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
	      <?php echo __('Additional names');?>
	      <?php if($orderBy=='additional_names') echo $orderSign ?>
	    </a>
          </th>
          <th class="datesNum">
	    <a class="sort" href="<?php echo url_for($s_url.'&orderby=birth_date'.( ($orderBy=='birth_date' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
	      <?php echo __('Life');?>
	      <?php if($orderBy=='birth_date') echo $orderSign ?>
	    </a>
          </th>
          <th class="datesNum">
	    <a class="sort" href="<?php echo url_for($s_url.'&orderby=activity_date_from'.( ($orderBy=='activity_date_from' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
	      <?php echo __('Activity');?>
	      <?php if($orderBy=='activity_date_from') echo $orderSign ?>
	    </a>
          </th>
          <th></th>
        <tr>
      </thead>
      <tbody>
        <?php foreach($items as $item):?>
          <tr class="rid_<?php echo $item->getId();?>">
            <td><?php echo image_tag('info.png',"title=info class=info");?></td>
            <td class="hidden item_name"><?php echo $item->getFormatedName();?></td>
            <td><?php echo $item->getTitle() ?></td>
            <td><?php echo $item->getFamilyName();?></td>
            <td><?php echo $item->getGivenName();?></td>
            <td><?php echo $item->getAdditionalNames() ?></td>
            <td class="datesNum">
	      <?php echo $item->getBirthDateObject()->getDateMasked('em','Y',ESC_RAW);?> -
	      <?php echo $item->getEndDateObject()->getDateMasked('em','Y',ESC_RAW) ?>
            </td>
            <td class="datesNum">
	      <?php echo $item->getActivityDateFromObject()->getDateMasked('em','Y',ESC_RAW);?> -
	      <?php echo $item->getActivityDateToObject()->getDateMasked('em','Y',ESC_RAW) ?>
            </td>
            <td class="<?php echo (! $is_choose)?'edit':'choose';?>">
              <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'people/view?id='.$item->getId(),array('target'=>"_blank"));?>
                <?php if(! $is_choose):?>                  
                  <?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
	                  <?php echo link_to(image_tag('edit.png',array('title'=>'Edit People')),'people/edit?id='.$item->getId());?>
	                  <?php echo link_to(image_tag('duplicate.png',array('title'=>'Duplicate People')),'people/new?duplicate_id='.$item->getId());?>
	                <?php endif ; ?>
                <?php else:?>
                  <?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
                    <?php echo link_to(image_tag('edit.png',array('title'=>'Edit People')),'people/edit?id='.$item->getId(),array('target'=>"_blank"));?>
                    <?php echo link_to(image_tag('duplicate.png',array('title'=>'Duplicate People')),'people/new?duplicate_id='.$item->getId(),array('target'=>"_blank"));?>
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
    <?php echo $form['activity_date_to']->renderError(); ?>
    <?php echo $form['people_type']->renderError(); ?>
    <?php echo $form['is_physical']->renderError(); ?>
    <?php echo $form['activity_date_from']->renderError(); ?>
    <?php echo $form['family_name']->renderError(); ?>
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
