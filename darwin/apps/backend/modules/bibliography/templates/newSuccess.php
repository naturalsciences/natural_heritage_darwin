<?php slot('title', __('Add bibliography'));  ?>
<div class="page">
    <h1 class="edit_mode"><?php echo __('New bibliography');?></h1>

    <?php include_partial('form', array('form' => $form)) ?>
    <?php include_partial('widgets/float_button', array('form' => $form,
                                                        'module' => 'bibliography',
                                                        'search_module'=>'bibliography/index',
                                                        'save_button_id' => 'submit')
    ); ?>
</div>
