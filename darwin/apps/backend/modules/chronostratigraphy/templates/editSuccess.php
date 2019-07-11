<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'catalogue_chronostratigraphy','eid'=> $form->getObject()->getId())); ?>
<?php slot('title', __('Edit Chronostratigraphic unit'));  ?>
<div class="page">
    <h1 class="edit_mode"><?php echo __('Edit Chronostratigraphic unit');?></h1>
    <?php if(count($no_right_col) > 0 && !$sf_user->isA(Users::ADMIN) ):?>
      <?php include_partial('catalogue/warnedit', array('no_right_col' => $no_right_col)); ?>
    <?php endif;?>
    <?php include_partial('form', array('form' => $form)); ?>
    <?php include_partial('widgets/float_button', array('form' => $form,
                                                        'module' => 'chronostratigraphy',
                                                        'search_module'=>'chronostratigraphy/index',
                                                        'save_button_id' => 'submit')
    ); ?>
    <?php include_partial('widgets/screen', array(
	'widgets' => $widgets,
	'category' => 'cataloguewidget',
	'columns' => 1,
	'options' => array('eid' => $form->getObject()->getId(), 'table' => 'chronostratigraphy')
	)); ?>

</div>
