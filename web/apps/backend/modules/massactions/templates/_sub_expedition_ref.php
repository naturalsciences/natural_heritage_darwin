  <table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['expedition_ref']['expedition_ref']->renderLabel();?>
      </th>
    </tr>
    <tr>
      <td>
        <?php echo $form['MassActionForm']['expedition_ref']['expedition_ref']->renderError();?>
        <?php echo $form['MassActionForm']['expedition_ref']['expedition_ref']->render(array('class' => 'inline'));?>
      </td>
    <tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
