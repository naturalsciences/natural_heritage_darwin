  <table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['station_visible']['station_visible']->renderLabel();?>
      </th>
      <td>
        <?php echo $form['MassActionForm']['station_visible']['station_visible']->renderError();?>
        <?php echo $form['MassActionForm']['station_visible']['station_visible']->render();?>
      </td>
    </tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
