<?php slot('widget_mandatory_refCollection',true);  ?>
<table>
  <tbody>
    <tr>
      <td colspan="2"><?php echo $form['collection_ref']->renderError() ?><td>
    </tr>  
    <tr>
      <td colspan="2">
      <?php if(isset($view) && $view) : ?>
        <?php echo $form->getObject()->Collections->getName() ; ?>
      <?php else  : ?>
        <?php echo $form['collection_ref']->render() ?>
      <?php endif; ?>
      </td>
    </tr>
  </tbody>
</table>  



