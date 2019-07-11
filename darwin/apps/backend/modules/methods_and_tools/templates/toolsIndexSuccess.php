<?php slot('title', __('Search Collecting Tools'));  ?>        
<div class="page">
  <h1><?php echo __('Collecting Tools Search');?></h1>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false, 'notion'=>'tool')) ?>
</div>
