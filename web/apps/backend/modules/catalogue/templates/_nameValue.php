<tr>  
  <td class="values">
      <?php echo $form->renderHiddenFields();?>
      <?php echo  sfInflector::humanize( sfInflector::tableize($form['keyword_type']->getValue()));?>
  </td>
  <td>
    <?php echo $form['keyword_type']->renderError();?>
    <?php echo $form['keyword'];?>
  </td>
  <td class="widget_row_delete">
      <?php echo image_tag('remove.png', 'alt=Delete class=clear_prop id='.$form['keyword_type']->getValue()); ?>
  </td>
</tr>
