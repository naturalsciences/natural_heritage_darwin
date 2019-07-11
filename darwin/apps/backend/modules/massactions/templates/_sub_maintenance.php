<table>
  <tbody>
    <tr>
      <th><?php echo $form['MassActionForm']['maintenance']['category']->renderLabel();?></th>
      <td>
        <?php echo $form['MassActionForm']['maintenance']['category']->renderError() ?>
        <?php echo $form['MassActionForm']['maintenance']['category'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['MassActionForm']['maintenance']['action_observation']->renderLabel();?></th>
      <td>
        <?php echo $form['MassActionForm']['maintenance']['action_observation']->renderError() ?>
        <?php echo $form['MassActionForm']['maintenance']['action_observation'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['MassActionForm']['maintenance']['modification_date_time']->renderLabel();?></th>
      <td>
        <?php echo $form['MassActionForm']['maintenance']['modification_date_time']->renderError() ?>
        <?php echo $form['MassActionForm']['maintenance']['modification_date_time'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['MassActionForm']['maintenance']['people_ref']->renderLabel();?></th>
      <td>
        <?php echo $form['MassActionForm']['maintenance']['people_ref']->renderError() ?>
        <?php echo $form['MassActionForm']['maintenance']['people_ref'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['MassActionForm']['maintenance']['description']->renderLabel();?></th>
      <td>
        <?php echo $form['MassActionForm']['maintenance']['description']->renderError() ?>
        <?php echo $form['MassActionForm']['maintenance']['description'];?>
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
