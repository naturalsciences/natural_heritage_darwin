<table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['social_status']['social_status']->renderLabel();?>
      </th>
      <td>
        <?php echo $form['MassActionForm']['social_status']['social_status']->renderError();?>
        <?php echo $form['MassActionForm']['social_status']['social_status'];?>
      </td>
    </tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
