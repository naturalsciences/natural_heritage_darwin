<?php slot('title', __('Loan Overview'));  ?>
<div class="page">
    <h1 class="view_mode"><?php echo __('Overview');?></h1>

    <?php include_partial('tabs', array('loan'=> $loan, 'items'=>array(),'view'=>true)); ?>
    <div class="tab_content panel_view">
        <table class="catalogue_table_view">
        <thead>
          <tr>
            <th><?php echo __('Darwin Part') ;?></th>
            <th><?php echo __('I.g. Num');?></th>
            <th><?php echo __('Details') ;?></th>
            <th><?php echo __('Expedition') ;?></th>
            <th><?php echo __('Return') ;?></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($items as $item):?>
            <td><?php echo $item->getPartRef();?></td>
            <td><?php echo $item->getIgRef();?></td>
            <td><?php echo $item->getDetails();?></td>
            <td><?php echo $item->getFromDate();?></td>
            <td><?php echo $item->getToDate();?></td>
            <td><?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'loanitem/view?id='.$item->getId());?></td>
          <?php endforeach;?>
        </tbody>
       </table>
       <?php if(! count($item)):?>
        <div class="warn_message"><?php echo __('There is currently no items in the loan.');?></div>
       <?php endif;?>
  <br />
      <p class="clear"></p>
      <p align="right">
        &nbsp;<a class="bt_close" href="<?php echo url_for('loan/edit?id='.$loan->getId()) ?>" id="spec_cancel"><?php echo __('Back to Loan');?></a>
      </p>

    </div>

</div>