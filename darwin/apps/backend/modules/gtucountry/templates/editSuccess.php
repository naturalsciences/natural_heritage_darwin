<?php slot('title', __('Edit Country'));  ?>        


<div class="page">
  <h1 class="edit_mode"><?php echo __('Edit Country' );?></h1>
    <?php if( !$sf_user->isA(Users::ADMIN) ):?>
      <?php include_partial('catalogue/warnedit', array('no_right_col' => "Only manager can create or edit a country")); ?>
    <?php endif;?>
  <?php include_partial('form', array('form' => $form)) ?>

</div>
