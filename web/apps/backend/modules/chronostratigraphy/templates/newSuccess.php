<?php slot('title', __('Add Chronostratigraphic unit'));  ?>
<div class="page">
    <h1 class="edit_mode"><?php echo __('New Chronostratigraphic unit');?></h1>

    <?php include_partial('form', array('form' => $form)) ?>
    <?php include_partial('widgets/float_button', array('form' => $form,
                                                        'module' => 'chronostratigraphy',
                                                        'search_module'=>'chronostratigraphy/index',
                                                        'save_button_id' => 'submit')
    ); ?>
</div>
