<?php slot('title', __('Search Bibliography'));  ?>
<div class="page">
  <h1><?php echo __('Bibliography Search');?></h1>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false)) ?>
</div>
