  <table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['col']['col']->renderLabel();?>
      </th>
      <td>
        <?php echo $form['MassActionForm']['col']['col']->renderError();?>
        <?php echo $form['MassActionForm']['col']['col'];?>
      </td>
    </tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
