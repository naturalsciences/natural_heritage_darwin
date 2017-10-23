<?php slot('title', __('Search Loans'));  ?>
<div class="page">
  <h1><?php echo __('Loans Search');?></h1>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false)) ?>
</div>
