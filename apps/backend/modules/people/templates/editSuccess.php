<?php slot('title', __('Edit People'));  ?>        
<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'people','eid'=> (! $form->getObject()->isNew() ? $form->getObject()->getId() : null ))); ?>
                                                                                               
<div class="page">
  <h1 class="edit_mode"><?php echo __('Edit People');?></h1>
  <?php include_partial('form', array('form' => $form)) ?>
	<?php include_partial('widgets/float_button', array('form' => $form,
																											'module' => 'people',
																											'search_module'=>'people/index',
																											'save_button_id' => 'submit')
	); ?>
  <?php include_partial('widgets/screen', array(
	'widgets' => $widgets,
	'category' => 'peoplewidget',
	'columns' => 1,
	'options' => array('eid' => $form->getObject()->getId(), 'table' => 'people')
	)); ?>
</div>
