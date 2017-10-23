<?php if(isset($view) && $view) : ?>
  <?php echo $form->getObject()->Chronostratigraphy->getName() ; ?>
<?php else  : ?>
  <?php echo $form['chrono_ref']->renderError() ?>
  <?php echo $form['chrono_ref']->render() ?>
<?php endif ; ?>
