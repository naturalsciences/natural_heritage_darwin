<table>
  <thead>
    <tr>
      <th><?php echo $form['type']->renderLabel();?></th>
    </tr>
  </thead>
  <tbody>
  <tr>
      <td>All : <input type="checkbox" id="check_all_types" name="check_all_types"></td>
    </tr>
    <tr>
      <td><?php echo $form['type'];?></td>
    </tr>
  </tbody>
</table>
<script language="javascript">
//ftheeten 2018 09 27
$(document).ready(
    function()
    {
        $("#check_all_types").change(
            function()
            {
               if($("#check_all_types").is(":checked"))
               {
                  $(".search_type_class[value!='specimen']").prop('checked', true);
               }
               else
               {
                    $(".search_type_class").prop('checked', false);
               }
            }
        );
    }
);
</script>
