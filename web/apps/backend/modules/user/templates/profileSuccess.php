<?php slot('title', __('My Profile'));  ?>       
<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'users', 'eid' => $user->getId())); ?>
<div class="page">
  <h1 class="edit_mode"><?php echo __("Edit My Profile");?></h1>
  <?php include_partial('form', array('form' => $form, 'mode' => $mode, 'user' => $user)) ?> 

  <?php include_partial('widgets/screen', array(
	'widgets' => $widgets,
	'category' => 'userswidget',
	'columns' => 1,
	'options' => array('eid' => $user->getId(), 'table' => 'users')
	)); ?>
</div>
