<?php slot('title', __('Add Instution'));  ?>                                                                                                       
<div class="page">
  <h1 class="edit_mode"><?php echo __('New Institution');?></h1>

  <?php include_partial('form', array('form' => $form)) ?>
  <?php include_partial('widgets/float_button', array('form' => $form,
                                                      'module' => 'institution',
                                                      'search_module'=>'institution/index',
                                                      'save_button_id' => 'submit')
  ); ?>
</div>
