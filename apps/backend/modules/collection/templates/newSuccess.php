<?php slot('title', __('Add Collection'));  ?>
<div class="page">
    <h1 class="edit_mode"><?php echo __('New Collection');?></h1>

    <?php include_partial('form', array('form' => $form)) ?>
    <?php include_partial('widgets/float_button', array('form' => $form,
                                                        'module' => 'collection',
                                                        'search_module'=>'collection/index',
                                                        'save_button_id' => 'submit')
    ); ?>
</div>
