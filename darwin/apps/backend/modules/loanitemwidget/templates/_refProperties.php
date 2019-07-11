<?php if($eid) : ?>
<?php echo get_component('cataloguewidget', 'properties', array('table' => 'loan_items', 'eid' => $eid));?>
<?php else : ?>
<?php echo __('Please save your loan item in order to add properties') ?>
<?php endif; ?>

