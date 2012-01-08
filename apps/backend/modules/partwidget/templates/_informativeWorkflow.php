<?php if($eid) : ?>
<?php echo get_component('cataloguewidget', 'informativeWorkflow', array('table' => 'specimen_parts', 'eid' => $eid));?>
<?php else : ?>
<?php echo __('Please save your part in order to add workflows') ?>
<?php endif; ?>

