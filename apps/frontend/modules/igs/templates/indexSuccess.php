<?php slot('title', __('Search IG Number'));  ?>        
<div class="page">
  <h1><?php echo __('RBINS General Inventory Numbers List');?></h1>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false)) ?>
  <br /><br />
  <div class='new_link'>
    <a href="<?php echo url_for('igs/new') ?>"><?php echo __('New');?></a>
  </div>
</div>
