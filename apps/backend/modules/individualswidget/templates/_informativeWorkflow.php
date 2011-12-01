<?php if($eid) : ?>
<?php echo get_component('cataloguewidget', 'informativeWorkflow', array('table' => 'specimen_individuals', 'eid' => $eid));?>
<?php else : ?>
<?php echo __('Please save your individual in order to add workflows') ?>
<?php endif; ?>

