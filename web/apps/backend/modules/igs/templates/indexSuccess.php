<?php slot('title', __('Search I.G. Number'));  ?>
<div class="page">
  <h1><?php echo __('General Inventory Numbers Search');?></h1>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false)) ?>
</div>
