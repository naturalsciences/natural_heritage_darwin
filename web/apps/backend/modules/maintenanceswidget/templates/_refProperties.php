<?php if($eid) : ?>
<?php echo get_component('cataloguewidget', 'properties', array('table' => 'collection_maintenance', 'eid' => $eid));?>
<?php else : ?>
<?php echo __('Please save your loan in order to add properties') ?>
<?php endif; ?>
