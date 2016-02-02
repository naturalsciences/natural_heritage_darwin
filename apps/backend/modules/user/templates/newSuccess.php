<?php slot('title', __('Add User'));  ?>                                                                                                       
<div class="page">
  <h1 class="edit_mode"><?php echo __("New User");?></h1>

  <?php include_partial('form', array('form' => $form, 'mode' => $mode)) ?>
  <?php include_partial('widgets/float_button', array('form' => $form,
                                                      'module' => 'user',
                                                      'search_module'=>'user/index',
                                                      'save_button_id' => 'submit',
                                                      'no_duplicate'=>true)
  ); ?>
</div>
