<table class="catalogue_widget_view">
  <head>
    <tr>
      <th><?php echo $form['specimen_status']->renderLabel() ?></th>
      <th><?php echo $form['count']->renderLabel() ?></th>
    </tr>
  </head>
  <tbody>
    <tr>
      <td><?php echo $form['specimen_status']->render() ?></td>
      <td>
        <?php echo $form['count_operator']->render() ?>
        <?php echo $form['count']->render() ?>
      </td>
    </tr>
  </tbody>
</table>
