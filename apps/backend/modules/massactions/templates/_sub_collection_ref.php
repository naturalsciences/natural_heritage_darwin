  <table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['collection_ref']['collection_ref']->renderLabel();?>
      </th>
    </tr>
    <tr>
      <td>
        <?php echo $form['MassActionForm']['collection_ref']['collection_ref']->renderError();?>
        <?php echo $form['MassActionForm']['collection_ref']['collection_ref']->render(array('class' => 'inline'));?>
      </td>
    <tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
   $('#mass_action_MassActionForm_collection_ref_collection_ref').change(function ()
    {
      changeSubmit(true);
    });
  });
  </script>
