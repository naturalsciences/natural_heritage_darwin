<table>
  <tbody>
  <tr>
    <th><?php echo $form['MassActionForm']['related_files']['visible']->renderLabel();?></th>
    <td>
      <?php echo $form['MassActionForm']['related_files']['visible']->renderError() ?>
      <?php echo $form['MassActionForm']['related_files']['visible'];?>
    </td>
  </tr>
  <tr>
    <th><?php echo $form['MassActionForm']['related_files']['publishable']->renderLabel();?></th>
    <td>
      <?php echo $form['MassActionForm']['related_files']['publishable']->renderError() ?>
      <?php echo $form['MassActionForm']['related_files']['publishable'];?>
    </td>
  </tr>
  </tbody>
</table>
<script  type="text/javascript">
  $(document).ready(function ()
  {
    changeSubmit(true);
  });

</script>
