<?php slot('title', __('Search in multimedia'));  ?>
<div class="page">
  <h1><?php echo __('Search in multimedia');?></h1>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false)) ?>
</div>
