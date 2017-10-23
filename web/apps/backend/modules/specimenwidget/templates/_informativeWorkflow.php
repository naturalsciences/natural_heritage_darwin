<?php if($eid) : ?>
<?php echo get_component('cataloguewidget', 'informativeWorkflow', array('table' => 'specimens', 'eid' => $eid));?>
<?php else : ?>
<?php echo __('Please save your specimen in order to add workflows') ?>
<?php endif; ?>

