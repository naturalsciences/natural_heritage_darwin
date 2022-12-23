<table>
  <tbody>
    <tr>
      <th><?php echo $form['MassActionForm']['informative_workflow']['status']->renderLabel();?></th>
      <td>
        <?php echo $form['MassActionForm']['informative_workflow']['status']->renderError() ?>
        <?php echo $form['MassActionForm']['informative_workflow']['status'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['MassActionForm']['informative_workflow']['comment']->renderLabel();?></th>
      <td>
        <?php echo $form['MassActionForm']['informative_workflow']['comment']->renderError() ?>
        <?php echo $form['MassActionForm']['informative_workflow']['comment'];?>
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
