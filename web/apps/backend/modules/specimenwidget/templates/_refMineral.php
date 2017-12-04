<?php if(isset($view) && $view) : ?>
  <?php echo $form->getObject()->Mineralogy->getName() ; ?>
<?php else  : ?>
  <?php echo $form['mineral_ref']->renderError() ?>
  <?php echo $form['mineral_ref']->render() ?>
<?php endif ; ?>
