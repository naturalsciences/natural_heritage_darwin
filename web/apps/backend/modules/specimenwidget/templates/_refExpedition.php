<?php if(isset($view) && $view) : ?>
  <?php echo $form->getObject()->Expeditions->getName() ; ?>
<?php else  : ?>
  <?php echo $form['expedition_ref']->renderError() ?>
  <?php echo $form['expedition_ref']->render() ?>
<?php endif ; ?>
