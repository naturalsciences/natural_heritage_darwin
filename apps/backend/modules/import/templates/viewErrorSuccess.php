<?php slot('title', __('Errors occured during import'));  ?>

<div class="page" id="stats" >
  <h1><?php echo __('Imports');?></h1>
   <h2><?php echo __("List of errors occured during import") ; ?></h2>

    <ul class="board_news">
    <?php foreach($errors as $error) : ?>
      <li><?php echo $error ?></li>
    <?php endforeach ; ?>
    </ul>
    <div class="warn_message">
    <?php echo __('<strong>Warning!</strong><br /> These errors cannot be corrected, the best way to remove it is to delete your import,
    correct your XML file and import it again. You can also continue your import but you won\'t have information above. What do you want to do ?');?>
    </div>
    <p>
      <a href="<?php echo url_for('import/maj?id='.$id) ?>" class="bt_close"><?php echo __('Continue import');?></a>
      <a href="<?php echo url_for('import/clear?id='.$id) ?>" class="bt_close"><?php echo __('Delete import');?></a>
    </p>

    <hr />
    <p>
      <a href="<?php echo url_for('import/index') ?>" class="bt_close"><?php echo __('Back');?></a>
    </p>

</div>

