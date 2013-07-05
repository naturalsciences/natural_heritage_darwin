<?php slot('title', __('View Specimen'));  ?>
<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'specimen','eid'=> $specimen->getId(),'view' => true)); ?>
<?php use_stylesheet('widgets.css') ?>
<?php use_javascript('widgets.js') ?>
<?php use_javascript('button_ref.js') ?>
<div class="page">
  <div class="tabs_view">
    <a class="enabled selected" id="tab_0">
      &lt; <?php echo sprintf(__('Specimen s%d'),$specimen->getId());?> &gt;
    </a>
  </div>
  <div class="panel_view encod_screen edition" id="intro">
   <div>	
      <?php include_partial('widgets/screen', array(
        'widgets' => $widgets,
        'category' => 'specimenwidgetview',	
        'columns' => 2,	
        'options' => array('eid'=> $specimen->getId(), 'level' => 2, 'view' => true),
      )); ?>
    </div>
    
    <p class="clear"></p>
    <p align="right">
      &nbsp;<a class="bt_close" href="<?php echo url_for('specimensearch/index') ?>" id="spec_cancel"><?php echo __('Back');?></a>
    </p>
  </div>
</div>
<script  type="text/javascript">

$(document).ready(function () {
//  $('body').duplicatable({duplicate_href: '<?php echo url_for('specimen/confirm');?>'});
  $('body').catalogue({});
  
});
</script>
