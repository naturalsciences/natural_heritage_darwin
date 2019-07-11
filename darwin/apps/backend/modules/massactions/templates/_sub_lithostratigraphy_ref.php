  <table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['lithostratigraphy_ref']['lithostratigraphy_ref']->renderLabel();?>
      </th>
    </tr>
    <tr>
      <td>
        <?php echo $form['MassActionForm']['lithostratigraphy_ref']['lithostratigraphy_ref']->renderError();?>
        <?php echo $form['MassActionForm']['lithostratigraphy_ref']['lithostratigraphy_ref']->render(array('class' => 'inline'));?>
      </td>
    <tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
