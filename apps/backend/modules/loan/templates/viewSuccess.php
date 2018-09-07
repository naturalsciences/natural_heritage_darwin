<?php slot('title', __('View Loan'));  ?>
<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'loan','eid'=> $loan->getid(),'view' => true)); ?>
<?php use_stylesheet('widgets.css') ?>
<?php use_javascript('widgets.js') ?>
<?php use_javascript('button_ref.js') ?>
<div class="page">
  <?php include_partial('tabs', array('loan'=> $loan, 'view' => true)); ?>

  <div class="panel_view encod_screen" id="intro">
   <div>
      <?php include_partial('widgets/screen', array(
        'widgets' => $widgets,
        'category' => 'loanwidgetview',
        'columns' => 1,
        'options' => array('eid'=> $loan->getid(), 'level' => 2, 'view' => true),
      )); ?>
    </div>

    <p class="clear"></p>
    <p align="right">
      &nbsp;<a class="bt_close" href="<?php echo url_for('loan/index') ?>" id="spec_cancel"><?php echo __('Back');?></a>
    </p>
  </div>
</div>
<script  type="text/javascript">

$(document).ready(function () {
  $('body').catalogue({});
});
</script>
