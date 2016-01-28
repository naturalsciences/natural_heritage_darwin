<?php slot('title', __('Add Expedition'));  ?>
<div class="page">
    <h1 class="edit_mode"><?php echo __('New Expedition');?></h1>

    <?php include_partial('form', array('form' => $form)) ?>
    <?php include_partial('widgets/float_button', array('form' => $form,
                                                        'module' => 'expeditionsIgs',
                                                        'search_module'=>'expeditionsIgs/index',
                                                        'save_button_id' => 'submit')
    ); ?>
</div>
