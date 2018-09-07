<?php slot('title', __('Search Collecting Methods'));  ?>        
<div class="page">
  <h1><?php echo __('Collecting Methods Search');?></h1>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false, 'notion'=>'method')) ?>
</div>
