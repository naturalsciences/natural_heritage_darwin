<?php slot('widget_mandatory_refCollection',true);  ?>
<?php echo $form['collection_ref']->renderError() ?>

<?php if(isset($view) && $view) : ?>
  <?php echo $form->getObject()->Collections->getName() ; ?>
<?php else  : ?>
  <?php echo $form['collection_ref']->render() ?>
<?php endif; ?>

