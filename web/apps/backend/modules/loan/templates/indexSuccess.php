<?php slot('title', __('Search Loans'));  ?>
<?php use_javascript("print_report.js"); ?>
<div class="page">
  <h1><?php echo __('Loans Search');?></h1>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false)) ?>
</div>
