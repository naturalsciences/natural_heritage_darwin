<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<?php $sFormClass = get_class($form->getEmbeddedForm('MassActionForm'));?>
<?php if(isset($mAction)):?>
  <div id="sub_form_<?php echo $mAction;?>" class="sub_form_block">
    <?php $actions_by_group = $form->getPossibleActions(true);?>
    <?php if(isset($actions_by_group[$mAction])):?>
      <h2><?php echo $actions_by_group[$mAction];?></h2>
      <?php include_partial('sub_'.$mAction,array('form'=>$form, 'mAction' => $mAction));?>
    <?php endif;?>
  </div>

<?php elseif($sFormClass == 'sfForm'):?>
  <?php echo $form['MassActionForm'];?>

<?php else:?>
  <div class="warning"><?php echo __("Houston, We've Got a Problem");?></div>
<?php endif;?>
