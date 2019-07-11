  <table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['acquisition_category']['acquisition_category']->renderLabel();?>
      </th>
    </tr>
    <tr>
      <td>
        <?php echo $form['MassActionForm']['acquisition_category']['acquisition_category']->renderError();?>
        <?php echo $form['MassActionForm']['acquisition_category']['acquisition_category']->render(array('class' => 'inline'));?>
      </td>
    <tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
