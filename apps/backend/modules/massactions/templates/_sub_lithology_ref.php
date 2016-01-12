  <table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['lithology_ref']['lithology_ref']->renderLabel();?>
      </th>
    </tr>
    <tr>
      <td>
        <?php echo $form['MassActionForm']['lithology_ref']['lithology_ref']->renderError();?>
        <?php echo $form['MassActionForm']['lithology_ref']['lithology_ref']->render(array('class' => 'inline'));?>
      </td>
    <tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
