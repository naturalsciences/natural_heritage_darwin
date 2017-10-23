<?php slot('title', __('Search Expeditions'));  ?>        
<div class="page">
  <h1><?php echo __('Expedition Search');?></h1>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false)) ?>
</div>
