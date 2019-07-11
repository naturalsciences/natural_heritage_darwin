<?php if(isset($notion)):?>
  <?php slot('title', __('New '.$notion));  ?>
  <div class="page">
      <h1 class="edit_mode"><?php echo __('New '.$notion);?></h1>
      <?php include_partial('form', array('form' => $form, 'notion'=>$notion)) ?>
  </div>
<?php else:?>
  <?php echo __('Please define if you wish to add a new tool or method');?>
<?php endif;?>
