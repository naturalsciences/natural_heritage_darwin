<table>
  <tbody>
    <?php echo $form->renderGlobalErrors() ?>
    <tr>
      <th><?php echo $form['name']->renderLabel() ?></th>
      <td>
        <?php echo $form['name']->renderError() ?>
        <?php echo $form['name'] ?>
      </td>
      <th><?php echo $form['from_date']->renderLabel() ?></th>
      <td>
        <?php echo $form['from_date']->renderError() ?>
        <?php echo $form['from_date'] ?>
      </td>

      <th><?php echo $form['extended_to_date']->renderLabel() ?></th>
      <td>
        <?php echo $form['extended_to_date']->renderError() ?>
        <?php echo $form['extended_to_date'] ?>
      </td>
    </tr>
    <tr>
      <th></th>
      <td></td>

      <th><?php echo $form['to_date']->renderLabel() ?></th>
      <td>
        <?php echo $form['to_date']->renderError() ?>
        <?php echo $form['to_date'] ?>
      </td>

      <th><?php echo $form['effective_to_date']->renderLabel() ?></th>
      <td>
        <?php echo $form['effective_to_date']->renderError() ?>
        <?php echo $form['effective_to_date'] ?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['description']->renderLabel() ?></th>
      <td colspan="5">
        <?php echo $form['description']->renderError() ?>
        <?php echo $form['description'] ?>
      </td>
    </tr>
  </tbody>
</table>