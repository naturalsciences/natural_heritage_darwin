<?php include_partial('widgets/list', array('widgets' => $widgets, 'category' => 'loans','eid'=> $form->getObject()->getId() )); ?>
<?php slot('title', __('Edit Loan'));  ?>
<div class="page">
    <h1 class="edit_mode"><?php echo __('Edit Loan');?></h1>
    <?php include_partial('tabs', array('loan'=> $form->getObject())); ?>
    <div class="tab_content">
      <?php include_partial('form', array('form' => $form)) ?>
    </div>
 <?php include_partial('widgets/screen', array(
	'widgets' => $widgets,
	'category' => 'loans',
	'columns' => 1,
	'options' => array('eid' => $form->getObject()->getId(), 'table' => 'loans')
	)); ?>
</div>
