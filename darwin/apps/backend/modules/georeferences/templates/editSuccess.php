<?php slot('title', __('Edit Georeference'));  ?>        


<div class="page">
  <h1 class="edit_mode"><?php echo __('Edit Georeference');?></h1>
   <?php $partial="form";?>

    <?php if(count($no_right_col) > 0 && !$sf_user->isA(Users::ADMIN) ):?>
      <?php include_partial('catalogue/warnedit', array('no_right_col' => $no_right_col)); ?>
    <?php endif;?>
  <?php include_partial($partial, array('form' => $form)) ?>
  <?php include_partial('widgets/float_button', array('form' => $form,
                                                      'module' => 'gtu',
                                                      'search_module'=>'gtu/index',
                                                      'save_button_id' => 'submit')
  ); ?>

</div>
