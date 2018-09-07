  <table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['row']['row']->renderLabel();?>
      </th>
      <td>
        <?php echo $form['MassActionForm']['row']['row']->renderError();?>
        <?php echo $form['MassActionForm']['row']['row'];?>
      </td>
    </tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
