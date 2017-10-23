<?php if(isset($view) && $view) : ?>
  <?php echo $form->getObject()->Lithology->getName() ; ?>
<?php else  : ?>
  <?php echo $form['lithology_ref']->renderError() ?>
  <?php echo $form['lithology_ref']->render() ?>
<?php endif ; ?>
