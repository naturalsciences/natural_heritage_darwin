  <table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['restricted_access']['restricted_access']->renderLabel();?>
      </th>
    </tr>
    <tr>
      <td>
        <?php echo $form['MassActionForm']['restricted_access']['restricted_access']->renderError();?>
        <?php echo $form['MassActionForm']['restricted_access']['restricted_access']->render(array('class' => 'inline'));?>
      </td>
    <tr>
  </table>

  <script  type="text/javascript">
   $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
