  <table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['chronostratigraphy_ref']['chronostratigraphy_ref']->renderLabel();?>
      </th>
    </tr>
    <tr>
      <td>
        <?php echo $form['MassActionForm']['chronostratigraphy_ref']['chronostratigraphy_ref']->renderError();?>
        <?php echo $form['MassActionForm']['chronostratigraphy_ref']['chronostratigraphy_ref']->render(array('class' => 'inline'));?>
      </td>
    <tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  </script>
