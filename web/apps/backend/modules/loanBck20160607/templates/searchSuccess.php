<?php if($form->isValid()):?>
<?php if(isset($items) && $items->count() != 0):?>
  <?php
    if($orderDir=='asc')
      $orderSign = '<span class="order_sign_down">&nbsp;&#9660;</span>';
    else
      $orderSign = '<span class="order_sign_up">&nbsp;&#9650;</span>';
  ?>

  <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
  <?php include_partial('global/pager_info', array('form' => $form, 'pagerLayout' => $pagerLayout, 'container' => '#loans_filter .results_container')); ?>
  <div class="results_container">
    <table class="results <?php if($is_choose) echo 'is_choose';?>">
      <thead>
        <tr>
          <th class="hidden"></th>
          <th>
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=name'.( ($orderBy=='name' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Name');?>
              <?php if($orderBy=='name') echo $orderSign ?>
            </a>
          </th>
          <th>
	      <?php echo __('Status');?>
          </th>
          <th>
	    <a class="sort" href="<?php echo url_for($s_url.'&orderby=from_date'.( ($orderBy=='from_date' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
	      <?php echo __('From');?>
	      <?php if($orderBy=='from_date') echo $orderSign ?>
	    </a>
          </th>
          <th>
	    <a class="sort" href="<?php echo url_for($s_url.'&orderby=to_date'.( ($orderBy=='to_date' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
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
          <tr class="rid_<?php echo $item->getId();?> <?php if(isset($status[$item->getId()]) && $status[$item->getId()]->getStatus() =='closed') echo 'loan_line_closed';?>">
            <td class="item_name"><?php echo $item->getName();?></td>
            <td class="loan_status_col"><?php if(isset($status[$item->getId()])):?>
                <?php echo $status[$item->getId()]->getFormattedStatus(); ?>
                <?php if($status[$item->getId()]->getStatus() =='closed'):?>
                  <em>(<?php echo __('on %date%',array('%date%'=> $status[$item->getId()]->getDate() ));?>)</em>
                <?php endif?>
              <?php endif?>
            </td>
            <td class="datesNum">
              <?php echo $item->getFromDateFormatted();?>
            </td>
            <td class="datesNum <?php if($item->getIsOverdue()) echo 'loan_overdue';?>">
              <?php if($item->getExtendedToDateFormatted() != ''):?>
                <?php echo $item->getExtendedToDateFormatted();?>
              <?php else:?>
                <?php echo $item->getToDateFormatted();?>
              <?php endif;?>
            </td>
            <td>
              <?php echo $item->getDescription();?>
            </td>
            <td class="">
              <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'loan/view?id='.$item->getId());?>            
              <?php if(in_array($item->getId(),sfOutputEscaper::unescape($rights)) || $sf_user->isAtLeast(Users::ADMIN)) : ?>
              <?php echo link_to(image_tag('edit.png',array('title'=>__('Edit loan'))),'loan/edit?id='.$item->getId());?>
			  <?php echo link_to("PDF", "http://172.16.11.138:8080/pentaho/api/repos/%3Apublic%3ADarwin2%3AReports_loans%3Aloans_april2016.prpt/report?LOAN_ID=".$item->getId()."&output-target=pageable%2Fpdf&accepted-page=-1&showParameters=true&renderMode=REPORT&htmlProportionalWidth=true", array("target"=> "_blank"));?>
              <?php endif ; ?>
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
