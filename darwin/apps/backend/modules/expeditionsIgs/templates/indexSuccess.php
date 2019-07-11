<?php slot('title', __('Search Expeditions and I.G.'));  ?>
<div class="page">
  <h1><?php echo __('Expedition and I.G. Search');?></h1>
  <div class="warn_message">
    <?php echo __('Use this search if you look for an I.G Number via an Expedition, or if you look for an expedition via an I.G Number') ; ?>
  </div>
  <?php include_partial('searchForm', array('form' => $form)) ?>
</div>
