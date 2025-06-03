<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php use_javascript('button_ref.js') ?>
<?php slot('title', __('Add a georef'));  ?>

<div class="page">
  <h1 class="edit_mode"><?php echo __('New  georef');?></h1>

  <?php include_partial('form', array('form' => $form, "mode"=>"new")) ?>

</div>
