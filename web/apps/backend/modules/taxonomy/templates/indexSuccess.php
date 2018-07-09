<?php slot('title', __('Search Taxonomic unit'));  ?>        

<div class="page">
	<!--JMHerpers 2018 03 14-->
  <h1><?php echo __('Taxon Search');?></h1>
  <?php include_partial('catalogue/chooseItem', array('searchForm' => $searchForm, 'is_choose' => false)) ?>
</div>
