<?php slot('title', __('Search Users'));  ?>        
<div class="page">
  <h1><?php echo __("Users Search");?></h1>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false)) ?>
</div>
