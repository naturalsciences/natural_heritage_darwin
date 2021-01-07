<?php slot('title', __('Add A Georeference service'));  ?>                                                                                                       
<div class="page">
  <h1 class="edit_mode"><?php echo __('New Georeference Service');?></h1>
 <?php $partial="form";?>
  
  
  <?php include_partial($partial, array('form' => $form)) ?>
  <?php include_partial('widgets/float_button', array('form' => $form,
                                                      'module' => 'gtu',
                                                      'search_module'=>'gtu/index',
                                                      'save_button_id' => 'submit')
  ); ?>
</div>
