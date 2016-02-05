<?php slot('title', __('View Sampling Location'));  ?>        
<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'catalogue_gtu','eid'=> $form->getObject()->getId())); ?>

<div class="page">
  <h1 class="edit_mode"><?php echo __('View Sampling Location');?></h1>
  <?php if(count($no_right_col) > 0 && !$sf_user->isA(Users::ADMIN) ):?>
    <?php include_partial('catalogue/warnedit', array('no_right_col' => $no_right_col)); ?>
  <?php endif;?>
  <?php include_partial('form', array('form' => $form)) ?>

  <?php include_partial('widgets/screen', array(
    'widgets' => $widgets,
    'category' => 'cataloguewidget',
    'columns' => 1,
    'options' => array('eid' => $form->getObject()->getId(), 'table' => 'gtu')
  )); ?>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		//rmca 2015 10 15: code to disable the accidental update of the GTU
		$('input[type=text]').attr('readonly',true);
		//$('input[type=text]').attr('disabled',true);
		//disable click event on the map
			map.off('click', setPointFromEvent);
	});
</script>

