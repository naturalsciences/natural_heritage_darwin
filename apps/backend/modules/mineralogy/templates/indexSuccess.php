<?php slot('title', __('Search Mineralogic unit'));  ?>        

<div class="page">
  <h1><?php echo __('Mineralogic unit Search');?></h1>
  <?php include_partial('catalogue/chooseItem', array('searchForm' => $searchForm, 'is_choose' => false)) ?>
</div>
