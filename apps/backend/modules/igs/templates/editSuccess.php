<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'catalogue_igs','eid'=> $form->getObject()->getId())); ?>
<?php slot('title', __('Edit I.G. number'));  ?>
<div class="page">
    <h1 class="edit_mode"><?php echo __('Edit I.G.');?></h1>
    <?php if(count($no_right_col) > 0 && !$sf_user->isA(Users::ADMIN) ):?>
      <?php include_partial('catalogue/warnedit', array('no_right_col' => $no_right_col)); ?>
    <?php endif;?>
    <?php include_partial('form', array('form' => $form)) ?>
    <?php include_partial('widgets/float_button', array('form' => $form,
                                                        'module' => 'igs',
                                                        'search_module'=>'igs/index',
                                                        'save_button_id' => 'submit')
    ); ?>
    <?php include_partial('widgets/screen', array(
	'widgets' => $widgets,
	'category' => 'cataloguewidget',
	'columns' => 1,
	'options' => array('eid' => $form->getObject()->getId(), 'table' => 'igs')
	)); ?>

</div>
