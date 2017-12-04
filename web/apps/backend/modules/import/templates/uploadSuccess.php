<?php slot('title', __('Upload file management page'));  ?>        

<div class="page">
  <h1 class="edit_mode"><?php echo __('Import ') ; if($type!='taxon') echo __('Specimens') ; else echo __('Taxonomy') ; ?></h1>
  <?php include_partial('import/upload_file', array('form' => $form,'type' => $type)) ?>
</div>

