<?php slot('title', __('Search Taxonomic unit'));  ?>        

<div class="page">
  <h1><?php echo __('Taxonomic unit Search');?></h1>
  <?php include_partial('catalogue/chooseItem', array('searchForm' => $searchForm, 'is_choose' => false)) ?>
</div>
