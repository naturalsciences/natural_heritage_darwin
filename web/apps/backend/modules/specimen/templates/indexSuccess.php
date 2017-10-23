<?php slot('title', __('Search Specimens'));  ?>        
<div class="page">
  <h1><?php echo __('Specimen Search');?></h1>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false)) ?>
</div>
