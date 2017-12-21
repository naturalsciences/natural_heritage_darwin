<?php slot('title', __('Search Taxonomy metadata'));  ?>        
<div class="page">
  <h1><?php echo __('Taxonomy metadata');?></h1>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false)) ?>
</div>
