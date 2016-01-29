<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'catalogue_bibliography','eid'=> $form->getObject()->getId() )); ?>
<?php slot('title', __('Edit Bibliography'));  ?>
<div class="page">
  <h1 class="edit_mode"><?php echo __('Edit Bibliography');?></h1>
  <div class="warn_message">
    <?php echo __('<strong>Warning!</strong><br /> This unit might be used in items where you do no have encoding rights.<br/>Be sure of what you do!');?>
  </div>

  <?php include_partial('form', array('form' => $form)) ?>
  <?php include_partial('widgets/float_button', array('form' => $form,
                                                      'module' => 'bibliography',
                                                      'search_module'=>'bibliography/index',
                                                      'save_button_id' => 'submit')
  ); ?>
 <?php include_partial('widgets/screen', array(
	'widgets' => $widgets,
	'category' => 'cataloguewidget',
	'columns' => 1,
	'options' => array('eid' => $form->getObject()->getId(), 'table' => 'bibliography')
	)); ?>
</div>
