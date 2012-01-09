<?php if($eid) : ?>
<?php echo get_component('cataloguewidget', 'informativeWorkflow', array('table' => 'Loans', 'eid' => $eid));?>
<?php else : ?>
<?php echo __('Please save your loan in order to add workflows') ?>
<?php endif; ?>
