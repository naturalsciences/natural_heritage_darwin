<?php foreach($form['CollectionsRights'] as $form_value):?>
 <?php include_partial('coll_rights', array('form' => $form_value));?>
<?php endforeach;?>
<?php foreach($form['newVal'] as $form_value):?>
 <?php include_partial('coll_rights', array('form' => $form_value));?>
<?php endforeach;?>
