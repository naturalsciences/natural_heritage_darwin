<table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['building']['building']->renderLabel();?>
      </th>
      <td>
        <?php echo $form['MassActionForm']['building']['building']->renderError();?>
        <?php echo $form['MassActionForm']['building']['building'];?>
      </td>
    </tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
