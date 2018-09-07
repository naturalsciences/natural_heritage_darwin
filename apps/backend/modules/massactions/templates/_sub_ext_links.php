<table>
  <tbody>
  <tr>
    <th><?php echo $form['MassActionForm']['ext_links']['url']->renderLabel();?></th>
    <td>
      <?php echo $form['MassActionForm']['ext_links']['url']->renderError() ?>
      <?php echo $form['MassActionForm']['ext_links']['url'];?>
    </td>
  </tr>
  <tr>
    <th><?php echo $form['MassActionForm']['ext_links']['comment']->renderLabel();?></th>
    <td>
      <?php echo $form['MassActionForm']['ext_links']['comment']->renderError() ?>
      <?php echo $form['MassActionForm']['ext_links']['comment'];?>
    </td>
  </tr>
  <tr>
    <th><?php echo $form['MassActionForm']['ext_links']['type']->renderLabel();?></th>
    <td>
      <?php echo $form['MassActionForm']['ext_links']['type']->renderError() ?>
      <?php echo $form['MassActionForm']['ext_links']['type'];?>
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
