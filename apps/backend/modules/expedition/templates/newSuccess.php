<?php slot('title', __('Add Expedition'));  ?>
<div class="page">
    <h1 class="edit_mode"><?php echo __('New Expedition');?></h1>

    <?php include_partial('form', array('form' => $form)) ?>
    <?php include_partial('widgets/float_button', array('form' => $form,
                                                        'module' => 'expedition',
                                                        'search_module'=>'expedition/index',
                                                        'save_button_id' => 'submit')
    ); ?>
</div>
