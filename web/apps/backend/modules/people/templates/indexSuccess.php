<?php slot('title', __('Search People'));  ?>        
<div class="page">
  <h1><?php echo __('People Search');?></h1>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false)) ?>
</div>
