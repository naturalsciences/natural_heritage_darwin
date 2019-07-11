<ul class="board_news"><u><?php echo __("Help on available State") ; ?></u>
  <?php foreach($import->getStateList() as $name => $info) : ?>
    <?php if ($name) : ?>
      <li><b><?php echo __($import->getStateName($name)) ; ?></b> : <?php echo __($import->getStateInfo($name)) ; ?></li>
    <?php endif ; ?>
  <?php endforeach ; ?>
  <li><b><?php echo __('Actif') ; ?></b> : <?php echo __($import->getStateInfo('Actif')) ; ?></li>
</ul>
<br />
<ul class="board_news"><u><?php echo __("Help on available button") ; ?></u>
    <li><b><?php echo image_tag('edit.png',array('title'=>__('Edit import'))) ; ?></b> : <?php echo __("This button (only visible when your file is on Pending state) will allow you to edit or remove data") ; ?></li>
    <li><b><?php echo image_tag('remove_2.png',array('title'=>__('Abort import'))) ; ?></b> : <?php echo __("This button will cancel the importing of the file, your file will have the Aborted state, it will allow you to keep a trace of this import, even if the data were not imported") ; ?></li>
    <li><b><?php echo image_tag('remove.png',array('title'=>__('Delete import'))) ; ?></b> : <?php echo __("This will delete this line. Already imported lines will remain in the database. All other lines will be deleted without a trace.") ; ?></li>
    <li><b><?php echo image_tag('warning.png',array('title'=>__('See errors during import'))) ; ?></b> : <?php echo __("warning_button_explanation") ; ?></li>
</ul>
