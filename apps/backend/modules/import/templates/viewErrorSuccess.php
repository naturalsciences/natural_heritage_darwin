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
      <?php if($import->getFormat() == 'abcd') : ?>
        <?php echo __('warning_spec_msg');?>
      <?php else: ?>
        <?php echo __('warning_catalogue_msg');?>
      <?php endif ?>
    </div>
    <p>
      <?php if($import->getFormat() == 'abcd') : ?><a href="<?php echo url_for('import/maj?id='.$id) ?>" class="bt_close"><?php echo __('Continue import');?></a><?php endif ;?>
      <a href="<?php echo url_for('import/clear?id='.$id) ?>" class="bt_close"><?php echo __('Delete import');?></a>
    </p>

    <hr />
    <?php if($import->getFormat() == 'taxon') : ?> 
      <p><?php echo link_to(__('Back'),'import/indexTaxon',array('class'=>'bt_close'));?></p>
    <?php else : ?>
      <p><?php echo link_to(__('Back'),'import/index',array('class'=>'bt_close'));?></p>
    <?php endif ; ?>

</div>

