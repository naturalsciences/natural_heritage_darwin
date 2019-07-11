<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'catalogue_collections','eid'=> $form->getObject()->getId())); ?>
<?php slot('title', __('Edit Collection'));  ?>
<div class="page">
  <h1 class="edit_mode"><?php echo __('Edit Collection');?></h1>

  <?php include_partial('form', array('form' => $form)) ?>
  <?php include_partial('widgets/float_button', array('form' => $form,
                                                      'module' => 'collection',
                                                      'search_module'=>'collection/index',
                                                      'save_button_id' => 'submit')
  ); ?>
  <?php include_partial('widgets/screen', array(
                        'widgets' => $widgets,
                        'category' => 'cataloguewidget',
                        'columns' => 1,
                        'options' => array('eid' => $form->getObject()->getId(), 'table' => 'collections', 'level' => $level)
                        )); ?>
</div>
