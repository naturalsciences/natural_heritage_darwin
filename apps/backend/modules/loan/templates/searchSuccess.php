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
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=title'.( ($orderBy=='name' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Name');?>
              <?php if($orderBy=='Name') echo $orderSign ?>
            </a>
          </th>
          <th>
	    <a class="sort" href="<?php echo url_for($s_url.'&orderby=status'.( ($orderBy=='status' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
	      <?php echo __('Status');?>
	      <?php if($orderBy=='family_name') echo $orderSign ?>
	    </a>
          </th>
          <th>
	    <a class="sort" href="<?php echo url_for($s_url.'&orderby=from_date'.( ($orderBy=='from_date' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
	      <?php echo __('From');?>
	      <?php if($orderBy=='from_date') echo $orderSign ?>
	    </a>
          </th>
          <th>
<!-- 	    <a class="sort" href="<?php echo url_for($s_url.'&orderby=to_date'.( ($orderBy=='to_date' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>"> -->
	      <?php echo __('To');?>
	      <?php if($orderBy=='to_date') echo $orderSign ?>
	    </a>
          </th>
          <th class="datesNum">
	    <a class="sort" href="<?php echo url_for($s_url.'&orderby=description'.( ($orderBy=='description' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
	      <?php echo __('Description');?>
	      <?php if($orderBy=='description') echo $orderSign ?>
	    </a>
          </th>
          <th></th>
        <tr>
      </thead>
      <tbody>
        <?php foreach($items as $item):?>
          <tr class="rid_<?php echo $item->getId();?>">
            <td><?php echo image_tag('info.png',"title=info class=info");?></td>
            <td class="item_name"><?php echo $item->getName();?></td>
            <td><?php echo $item->LoanStatus[0]->getStatus() ?></td>
            <td class="datesNum">
	      <?php echo $item->getFromDate();?>
            </td>
            <td class="datesNum">
              <?php echo $item->getToDate();?>
            </td>            
            <td>
              <?php echo $item->getDescription();?>
            </td>           
            <td class="">
              <?php echo link_to(image_tag('edit.png',array('title'=>'Edit loan')),'loan/edit?id='.$item->getId());?>
	      <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'loan/view?id='.$item->getId());?>
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
    <?php echo $form['from_date']->renderError(); ?>
    <?php echo $form['to_date']->renderError(); ?>
    <?php echo $form['status']->renderError(); ?>
    <?php echo $form['ig_ref']->renderError(); ?>
</div>
<?php endif;?>
<script>
 /* $("img.info").click(function() {
      item_row=$(this).closest('tr');
      el_id  = getIdInClasses(item_row);
      if($('.details_rid_'+el_id).is(":hidden"))
      {
	if($('.details_rid_'+el_id+' > td:first ').html() == '')
	{
	  $.get('<?php echo url_for('loan/details');?>/id/'+el_id,function (html){
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
  });*/
</script>
