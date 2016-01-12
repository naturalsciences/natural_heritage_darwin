<table>
  <tr>
  <th class="top_aligned"><?php echo $form['specimen_part']->renderLabel();?></th>
  <td>
    <?php echo $form['specimen_part']->renderError();?>
    <?php echo $form['specimen_part']->render() ?>
  </td>
  </tr>
  <tr>
  <th class="top_aligned"><?php echo $form['category']->renderLabel();?></th>
  <td>
    <?php echo $form['category']->renderError();?>
    <?php echo $form['category']->render() ?>
  </td>
  </tr>
  <tr>
  <th class="top_aligned"><?php echo $form['object_name']->renderLabel();?></th>
  <td>
    <?php echo $form['object_name']->renderError();?>
    <?php echo $form['object_name']->render() ?>
  </td>
  </tr>
</table>
