  <table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['acquisition_date']['acquisition_date']->renderLabel();?>
      </th>
    </tr>
    <tr>
      <td>
        <?php echo $form['MassActionForm']['acquisition_date']['acquisition_date']->renderError();?>
        <?php echo $form['MassActionForm']['acquisition_date']['acquisition_date']->render(array('class' => 'inline'));?>
      </td>
    <tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
