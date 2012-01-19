<?php slot('title', __('View specimen individual'));  ?>
<?php include_partial('widgets/list', array('widgets' => $widgets, 'category' => 'individuals', 'table' => 'specimen_individuals','eid'=> $specimen->getIndividualRef(),'view' => true)); ?>
<?php use_stylesheet('widgets.css') ?>
<?php use_javascript('widgets.js') ?>
<?php use_javascript('button_ref.js') ?>
<div class="page">
  <div class="tabs_view">
		<?php echo link_to(__('View Specimen'), 'specimen/view?id='.$specimen->getSpecRef(), array('class'=>'enabled', 'id' => 'tab_0'));?>  
    <?php echo link_to(__('Individual overview'), 'individuals/overview?spec_id='.$specimen->getSpecRef().'&view=true', array('id'=>'tab_1', 'class'=> 'enabled')); ?>
    <a class="enabled selected" id="tab_2"> &lt; <?php echo sprintf(__('Individual %d'),$specimen->getIndividualRef());?> &gt; </a>
    <?php echo link_to(__('Parts overview'), 'parts/overview?id='.$specimen->getIndividualRef().'&view=true', array('id'=>'tab_3', 'class'=> 'enabled')); ?>
  </div>

  <div class="panel_view encod_screen edition" id="intro">
   <div>	
      <?php include_partial('widgets/screen', array(
        'widgets' => $widgets,
        'category' => 'individualswidgetview',	
        'columns' => 2,	
        'options' => array('eid'=> $specimen->getIndividualRef(), 'level' => 2, 'view' => true),
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
  $('body').catalogue({});
  check_screen_size() ;
  $(window).resize(function(){
    check_screen_size();
  });
});
</script>  
