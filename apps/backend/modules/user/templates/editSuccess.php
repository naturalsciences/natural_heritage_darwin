<?php slot('title', __('Edit Profile'));  ?>        
<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'users','eid'=> $user->getId())); ?>
<div class="page">
  <h1 class="edit_mode"><?php echo sprintf(__("Profile for %s"), $user->getFormatedName() ) ; ?></h1>
  <?php include_partial('form', array('form' => $form, 'mode' => $mode, 'user' => $user)) ?>
	<?php include_partial('widgets/float_button', array('form' => $form,
																											'module' => 'user',
																											'search_module'=>'user/index',
																											'save_button_id' => 'submit',
																											'no_duplicate'=>true)
	); ?>
  <?php include_partial('widgets/screen', array(
	'widgets' => $widgets,
	'category' => 'userswidget',
	'columns' => 1,
	'options' => array('eid' => $user->getId(), 'table' => 'users')
	)); ?>
</div>
