<?php if($form->hasError()):?>
  <tr>
    <td>
      <?php echo $form->renderError();?>
    </td>
  </tr>
<?php endif;?>
<tr>
<?php if($form->offsetExists('people_ref')) : ?>
  <td><?php echo $form['people_ref']->renderError();?></td>
</tr>
<tr class="spec_ident_collectors_data" id="<?php echo $id_field.'_'.$row_num; ?>">
  <td><?php echo $form['people_ref']->render();?>
  &nbsp;&nbsp;(<?php echo __("defined as %role%",array("%role%" => $form['people_type']->getValue())); ?>)</td>
</tr>
<?php else : ?>
  <td><?php echo $form['institution_ref']->renderError();?></td>
</tr>
<tr class="spec_ident_collectors_data" id="<?php echo $id_field.'_'.$row_num; ?>">
  <td><?php echo $form['institution_ref']->render();?></td>
</tr>
<?php endif ; ?>
