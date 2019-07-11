<?php slot('title', __('View loan'));  ?>
<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'loanitem','eid'=> $loan_item->getid(),'view' => true)); ?>
<?php use_stylesheet('widgets.css') ?>
<?php use_javascript('widgets.js') ?>
<?php use_javascript('button_ref.js') ?>
<div class="page">
  <?php include_partial('loan/tabs', array('loan'=> $loan_item->getLoan(), 'item' => $loan_item, 'view' => true)); ?>

  <div class="panel_view encod_screen" id="intro">
   <div>        
      <?php include_partial('widgets/screen', array(
        'widgets' => $widgets,
        'category' => 'loanitemwidgetview',
        'columns' => 1, 
        'options' => array('eid'=> $loan_item->getid(), 'level' => 2, 'view' => true),
      )); ?>
    </div>
    
    <p class="clear"></p>
    <p align="right">
      &nbsp;<a class="bt_close" href="<?php echo url_for('loanitem/index') ?>" id="spec_cancel"><?php echo __('Back');?></a>
    </p>
  </div>
</div>
<script  type="text/javascript">

$(document).ready(function () {
  $('body').catalogue({});
});
</script>
