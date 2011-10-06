<?php slot('title', __('View specimen part'));  ?>
<?php include_partial('widgets/list', array('widgets' => $widgets, 'category' => 'part', 'table' => 'specimen_parts','eid'=> $part->getId(),'view' => true)); ?>
<?php use_stylesheet('widgets.css') ?>
<?php use_javascript('widgets.js') ?>
<?php use_javascript('catalogue.js') ?>
<?php use_javascript('button_ref.js') ?>
<div class="page">
  <div class="tabs_view">
    <?php echo link_to(__('View Specimen'), 'specimen/view?id='.$part->Individual->Specimens->getId(), array('class'=>'enabled', 'id' => 'tab_0'));?>  
    <?php echo link_to(__('Individual overview'), 'individuals/overview?spec_id='.$part->Individual->Specimens->getId().'&view=true', array('id'=>'tab_1', 'class'=> 'enabled')); ?>
    <?php echo link_to(__('Individual'.$part->Individual->getId()), 'individuals/view?id='.$part->Individual->getId(), array('id'=>'tab_2', 'class'=> 'enabled')); ?>
    <?php echo link_to(__('Part overview'), 'parts/overview?id='.$part->Individual->getId()."&view=true", array('class'=>'enabled', 'id' => 'tab_3'));?>
    <a class="enabled selected" id="tab_4"> &lt; <?php echo sprintf(__('Part %d'), $part->getId());?> &gt; </a>		
  </div>

  <div class="panel_view encod_screen edition" id="intro">
   <div>	
      <?php include_partial('widgets/screen', array(
        'widgets' => $widgets,
        'category' => 'partwidgetview',	
        'columns' => 2,	
        'options' => array('eid'=>  $part->getId(), 'level' => 2, 'view' => true),
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
  check_screen_size() ;
  $(window).resize(function(){
    check_screen_size();
  });
});
</script>  
