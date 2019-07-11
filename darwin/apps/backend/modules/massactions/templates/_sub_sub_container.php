  <table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['sub_container']['sub_container']->renderLabel();?>
      </th>
      <td>
        <?php echo $form['MassActionForm']['sub_container']['sub_container']->renderError();?>
        <?php echo $form['MassActionForm']['sub_container']['sub_container'];?>
      </td>
    </tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
