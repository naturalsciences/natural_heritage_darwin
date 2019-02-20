  <table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['shelf']['shelf']->renderLabel();?>
      </th>
      <td>
        <?php echo $form['MassActionForm']['shelf']['shelf']->renderError();?>
        <?php echo $form['MassActionForm']['shelf']['shelf'];?>
      </td>
    </tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
