<?php slot('title', __('Search in comments'));  ?>
<div class="page">
  <h1><?php echo __('Search in comments');?></h1>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false)) ?>
</div>
