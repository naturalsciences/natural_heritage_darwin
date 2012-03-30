<?php if($code_copy):?>
  <div class="warn_message"><?php echo __("The specimen code will be copied automaticaly.");?></div>
<?php endif;?>
<?php include_partial('specimenwidget/refCodes',  array('module' => 'parts', 'form' => $form));?>