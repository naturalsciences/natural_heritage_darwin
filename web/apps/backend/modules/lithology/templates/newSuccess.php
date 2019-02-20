<?php slot('title', __('Add Lithologic unit'));  ?>
<div class="page">
    <h1 class="edit_mode"><?php echo __('New Lithologic unit');?></h1>

    <?php include_partial('form', array('form' => $form)) ?>
    <?php include_partial('widgets/float_button', array('form' => $form,
                                                        'module' => 'lithology',
                                                        'search_module'=>'lithology/index',
                                                        'save_button_id' => 'submit')
    ); ?>
</div>
