<table>
  <tbody>
    <tr>
      <th>
        <?php echo $form['acquisition_category']->renderLabel(); ?>
      </th>
      <td>
        <?php echo $form['acquisition_category']->render(); ?>
      </td>
    </tr>
    <tr>
      <th>
        <?php echo $form['acquisition_from_date']->renderLabel() ?>
      </th>
      <th>
        <?php echo $form['acquisition_to_date']->renderLabel() ?>
      </th>
    </tr>
    <tr>
      <th>
        <?php echo $form['acquisition_from_date']->render() ?>
      </th>
      <td>
        <?php echo $form['acquisition_to_date']->render() ?>
      </td>
    </tr>    
  </tbody>
</table>
