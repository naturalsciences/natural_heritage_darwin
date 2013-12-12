<?php slot('title', __('Search in properties'));  ?>
<div class="page">
  <h1><?php echo __('Search in properties');?></h1>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false)) ?>
</div>
