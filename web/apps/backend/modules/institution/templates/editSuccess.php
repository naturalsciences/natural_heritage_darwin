<?php slot('title', __('Edit Institution'));  ?>        
<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'people_institution','eid'=> $form->getObject()->getId())); ?>
                                                                                               
<div class="page">
  <h1 class="edit_mode"><?php echo __("Edit Institution");?></h1>
  <?php include_partial('form', array('form' => $form)) ?>
	<?php include_partial('widgets/float_button', array('form' => $form,
																											'module' => 'institution',
																											'search_module'=>'institution/index',
																											'save_button_id' => 'submit')
	); ?>

	<?php include_partial('widgets/screen', array(
	  'widgets' => $widgets,
		'category' => 'peoplewidget',
		'columns' => 1,
		'options' => array('eid' => $form->getObject()->getId(), 'table' => 'institution')
		)); ?>

</div>
