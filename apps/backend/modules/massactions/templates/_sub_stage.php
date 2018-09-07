  <table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['stage']['stage']->renderLabel();?>
      </th>
      <td>
        <?php echo $form['MassActionForm']['stage']['stage']->renderError();?>
        <?php echo $form['MassActionForm']['stage']['stage'];?>
      </td>
    </tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
