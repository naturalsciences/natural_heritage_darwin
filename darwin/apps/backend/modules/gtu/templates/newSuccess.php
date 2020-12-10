<?php slot('title', __('Add A Sampling Location'));  ?>                                                                                                       
<div class="page">
  <h1 class="edit_mode"><?php echo __('New Sampling Location');?></h1>
    <?php include_partial('widgets/float_button', array('form' => $form,
                                                        'module' => 'igs',
                                                        'search_module'=>'igs/index',
                                                        'save_button_id' => 'submit')
    ); ?>
  <?php include_partial('form', array('form' => $form)) ?>
</div>