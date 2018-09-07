<table>
  <tr>
    <th>
      <?php echo $form['MassActionForm']['specimen_status']['specimen_status']->renderLabel();?>
    </th>
    <td>
      <?php echo $form['MassActionForm']['specimen_status']['specimen_status']->renderError();?>
      <?php echo $form['MassActionForm']['specimen_status']['specimen_status'];?>
    </td>
  </tr>
</table>

<script  type="text/javascript">
  $(document).ready(function () {
    changeSubmit(true);
  });
</script>
