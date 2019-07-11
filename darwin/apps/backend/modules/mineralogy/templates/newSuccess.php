<?php slot('title', __('Add Mineralogic unit'));  ?>
<div class="page">
    <h1 class="edit_mode"><?php echo __('New Mineralogic unit');?></h1>

    <?php include_partial('form', array('form' => $form)) ?>
    <?php include_partial('widgets/float_button', array('form' => $form,
                                                        'module' => 'mineralogy',
                                                        'search_module'=>'mineralogy/index',
                                                        'save_button_id' => 'submit')
    ); ?>
</div>
