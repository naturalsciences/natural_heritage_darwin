<?php slot('title', __('Add People'));  ?>                                                                                                       
<div class="page">
  <h1 class="edit_mode"><?php echo __('New People');?></h1>

  <?php include_partial('form', array('form' => $form)) ?>
  <?php include_partial('widgets/float_button', array('form' => $form,
                                                      'module' => 'people',
                                                      'search_module'=>'people/index',
                                                      'save_button_id' => 'submit')
  ); ?>
</div>
