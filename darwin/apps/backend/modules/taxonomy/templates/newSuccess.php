<?php slot('title', __('Add Taxonomic unit'));  ?>
<div class="page">
    <h1 class="edit_mode"><?php echo __('New Taxonomic unit');?></h1>
    <?php include_partial('form', array('form' => $form)) ?>
    <?php include_partial('widgets/float_button', array('form' => $form,
                                                        'module' => 'taxonomy',
                                                        'search_module'=>'taxonomy/index',
                                                        'save_button_id' => 'submit')
    ); ?>
</div>
