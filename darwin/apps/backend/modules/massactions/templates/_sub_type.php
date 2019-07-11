<table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['type']['type']->renderLabel();?>
      </th>
      <td>
        <?php echo $form['MassActionForm']['type']['type']->renderError();?>
        <?php echo $form['MassActionForm']['type']['type'];?>
      </td>
    </tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
