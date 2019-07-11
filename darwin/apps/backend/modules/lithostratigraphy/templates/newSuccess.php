<?php slot('title', __('Add Lithostratigraphic unit'));  ?>
<div class="page">
    <h1 class="edit_mode"><?php echo __('New Lithostratigraphic unit');?></h1>

    <?php include_partial('form', array('form' => $form)) ?>
    <?php include_partial('widgets/float_button', array('form' => $form,
                                                        'module' => 'lithostratigraphy',
                                                        'search_module'=>'lithostratigraphy/index',
                                                        'save_button_id' => 'submit')
    ); ?>

</div>
