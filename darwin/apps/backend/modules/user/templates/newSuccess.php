<?php slot('title', __('Add User'));  ?>                                                                                                       
<div class="page">
  <h1 class="edit_mode"><?php echo __("New User");?></h1>

  <?php include_partial('form', array('form' => $form, 'mode' => $mode)) ?>
</div>
