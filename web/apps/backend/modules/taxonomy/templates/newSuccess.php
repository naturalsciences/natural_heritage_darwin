<?php slot('title', __('Add Taxonomic unit'));  ?>
<div class="page">
    <h1 class="edit_mode"><?php echo __('New Taxonomic unit');?></h1>
    <?php include_partial('form', array('form' => $form, 
    //ftheeten 2017 07 06
    'collection_ref_for_insertion'=> $collection_ref_for_insertion)) ?>
</div>