  <table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['floor']['floor']->renderLabel();?>
      </th>
      <td>
        <?php echo $form['MassActionForm']['floor']['floor']->renderError();?>
        <?php echo $form['MassActionForm']['floor']['floor'];?>
      </td>
    </tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
