<?php if($eid) : ?>
<?php echo get_component('cataloguewidget', 'properties', array('table' => 'SpecimenParts', 'eid' => $eid));?>
<?php else : ?>
<?php echo __('Please save your specimen in order to add properties') ?>
<?php endif; ?>
