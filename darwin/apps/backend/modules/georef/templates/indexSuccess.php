<?php slot('title', __('Search Georeferences'));  ?>        
<div class="page">
  <h1><?php echo __('Georeferences search');?></h1>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false)) ?>
</div>
