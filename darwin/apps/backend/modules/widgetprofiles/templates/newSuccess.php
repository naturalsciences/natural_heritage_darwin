<?php slot('title', __('Add profile'));  ?>
<div class="page">
    <h1 class="edit_mode"><?php echo __('New profile');?></h1>
    <?php include_partial('form', array('form' => $form, 'duplic'=>$duplic)) ?>
   
</div>
