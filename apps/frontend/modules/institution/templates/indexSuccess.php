<?php slot('title', __('Search Institutions'));  ?>        
<div class="page">
<h1>Institutions Search</h1>

  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false)) ?>

  <br /><br />
  <div class='new_link'>
    <a href="<?php echo url_for('institution/new') ?>"><?php echo __('New');?></a>
  </div>

</div>
