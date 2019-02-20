<?php slot('title', __('Add an I.G. number'));  ?>
<div class="page">
    <h1 class="edit_mode"><?php echo __('New I.G. number');?></h1>

    <?php include_partial('form', array('form' => $form)) ?>
    <?php include_partial('widgets/float_button', array('form' => $form,
                                                        'module' => 'igs',
                                                        'search_module'=>'igs/index',
                                                        'save_button_id' => 'submit')
    ); ?>
</div>
