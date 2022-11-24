<?php slot('title', __('Search Profiles'));  ?>        
<div class="page">
  <h1><?php echo __("Search Profiles");?></h1>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false)) ?>
</div>
