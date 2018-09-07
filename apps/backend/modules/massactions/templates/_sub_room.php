  <table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['room']['room']->renderLabel();?>
      </th>
      <td>
        <?php echo $form['MassActionForm']['room']['room']->renderError();?>
        <?php echo $form['MassActionForm']['room']['room'];?>
      </td>
    </tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
