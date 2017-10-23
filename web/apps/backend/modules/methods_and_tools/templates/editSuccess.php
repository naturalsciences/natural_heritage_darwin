<?php if(isset($notion)):?>
  <?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'catalogue_methods_and_tools','eid'=> $form->getObject()->getId(),'table'=>(($notion=='method')?'collecting_methods':'collecting_tools') )); ?>
  <?php slot('title', __('Edit '.$notion));  ?>
  <div class="page">
      <h1 class="edit_mode"><?php echo __('Edit '.$notion);?></h1>

      <?php include_partial('form', array('form' => $form, 'notion'=>$notion)) ?>

  <?php include_partial('widgets/screen', array(
    'widgets' => $widgets,
    'category' => 'cataloguewidget',
    'columns' => 1,
    'options' => array('eid' => $form->getObject()->getId(), 'table' => (($notion=='method')?'collecting_methods':'collecting_tools'))
    )); ?>
  </div>
<?php else:?>
  <?php echo __('Please define if you wish to edit a tool or method');?>
<?php endif;?>
