  <table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['nagoya_specimen']['nagoya_specimen']->renderLabel();?>
      </th>
      <td>
        <?php echo $form['MassActionForm']['nagoya_specimen']['nagoya_specimen']->renderError();?>
        <?php echo $form['MassActionForm']['nagoya_specimen']['nagoya_specimen']->render();?>
		
      </td>
    <tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {

      changeSubmit(true);

  });
  </script>