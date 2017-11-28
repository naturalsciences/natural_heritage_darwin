<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'catalogue_taxonomy','eid'=> $form->getObject()->getId())); ?>
<?php slot('title', __('Edit Taxonomic metadata'));  ?>
<div class="page">
    <h1 class="edit_mode"><?php echo __('Edit Taxonomic metadata');?></h1>
    
    <?php include_partial('form', array('form' => $form)); ?>
 
</div>
