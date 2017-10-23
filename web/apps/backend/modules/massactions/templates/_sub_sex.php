<table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['sex']['sex']->renderLabel();?>
      </th>
      <td>
        <?php echo $form['MassActionForm']['sex']['sex']->renderError();?>
        <?php echo $form['MassActionForm']['sex']['sex'];?>
      </td>
    </tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
