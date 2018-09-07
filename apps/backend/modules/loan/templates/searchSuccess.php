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
    <div id="error_message"></div>
    <table class="results <?php if(isset($is_choose) && $is_choose) echo 'is_choose';?>">
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
            <td class="item_name"><span class="item_name"><?php echo $item->getName();?></span></td>
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
            <td class="<?php echo ( isset( $is_choose ) && $is_choose ) ? 'choose' : 'edit';?>">
              <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'loan/view?id='.$item->getId());?>
              <?php if(! $is_choose):?>
                <?php if(in_array($item->getId(),sfOutputEscaper::unescape($rights)) || $sf_user->isAtLeast(Users::ADMIN)) : ?>
                  <?php echo link_to(image_tag('edit.png',array('title'=>__('Edit loan'))),'loan/edit?id='.$item->getId());?>
                  <?php echo link_to(image_tag('duplicate.png',array('title'=>__('Duplicate loan'))),'loan/new?duplicate_id='.$item->getId());?>
                  <?php echo link_to(image_tag('remove.png',array('title'=>__('Remove loan'))),'loan/delete?id='.$item->getId(), array('class'=>'clear_item'));?>
                <?php endif ; ?>
                <?php if (isset($printable) && in_array($item->getId(), $printable->getRawValue())): ?>
                  <?php echo link_to(
                    image_tag(
                      'print.png',
                      array(
                        'title'=>__('Print loan')
                      )
                    ),
                    'report/getReport?'.http_build_query(array(
                                                           'name'=>'loans_form_complete',
                                                           'default_vals'=>array(
                                                             'loan_id'=>$item->getId()
                                                           )
                                                         )),
                    array('class'=>'print_item')
                  );?>
                <?php endif; ?>
              <?php else:?>
                <?php if(in_array($item->getId(),sfOutputEscaper::unescape($rights)) || $sf_user->isAtLeast(Users::ADMIN)) : ?>
                  <?php echo link_to(image_tag('edit.png',array('title'=>__('Edit loan'))),'loan/edit?id='.$item->getId(),array('target'=>"_blank"));?>
                  <?php echo link_to(image_tag('duplicate.png',array('title'=>__('Duplicate loan'))),'loan/new?duplicate_id='.$item->getId(),array('target'=>"_blank"));?>
                <?php endif ; ?>
                <div class="result_choose"><?php echo __('Choose');?></div>
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
  <script type="text/javascript">
    $(document).ready(function () {
      $("div.results_container").results({ "confirmation_message" : "<?php echo addslashes(__('Are you sure ?'));?>" });
      $("div.results_container").print_report({ "q_tip_text" : "<?php echo addslashes(__('Please fill in the criterias to print your report'));?>" });
    });
  </script>
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

