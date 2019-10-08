  <table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['sampling_date']['gtu_from_date']->renderLabel();?>
      </th>
    </tr>
    <tr>
      <td>
        <?php echo $form['MassActionForm']['sampling_date']['gtu_from_date']->renderError();?>
        <?php echo $form['MassActionForm']['sampling_date']['gtu_from_date']->render(array('class' => 'inline'));?>
      </td>
    <tr>
        <tr>
      <th>
        <?php echo $form['MassActionForm']['sampling_date']['gtu_to_date']->renderLabel();?>
      </th>
    </tr>
    <tr>
      <td>
        <?php echo $form['MassActionForm']['sampling_date']['gtu_to_date']->renderError();?>
        <?php echo $form['MassActionForm']['sampling_date']['gtu_to_date']->render(array('class' => 'inline'));?>
      </td>
    <tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
