<?php if(isset($view) && $view) : ?>
  <?php echo $form->getObject()->Lithostratigraphy->getName() ; ?>
<?php else  : ?>
  <?php echo $form['litho_ref']->renderError() ?>
  <?php echo $form['litho_ref']->render() ?>
<?php endif ; ?>
