<?php slot('title', __('Search Institutions'));  ?>        
<div class="page">
  <h1><?php echo __("Institution Search");?></h1>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false)) ?>
</div>
