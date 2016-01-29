  <tr>
    <td>
      <?php echo $form['community']->renderError(); ?>
      <?php echo $form['community'];?>
      <?php echo $form['referenced_relation'];?>
      <?php echo $form['record_id'];?>
    </td>
    <td><?php echo $form['id'];?>
      <?php echo $form['name']->renderError(); ?>
      <?php echo $form['name'];?>
    </td>
    <td class="widget_row_delete">
      <?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?>
    </td>    
  </tr>
