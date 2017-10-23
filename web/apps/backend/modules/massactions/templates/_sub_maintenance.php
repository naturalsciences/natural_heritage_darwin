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

 /* function addMember(people_ref, people_name)
  { 

    info = 'ok';
    $('#exp_member_table tbody tr').each(function() {
      if($(this).find('input[id$=\"_people_ref\"]').val() == people_ref) info = 'bad' ;
    });
    if(info != 'ok') return false;

    $.ajax({
      type: "GET",
      url: $('.add_value a.hidden').attr('href')+ (0+$('#exp_member_table tbody tr').length)+'/people_ref/'+people_ref + '/iorder_by/' + (0+$('#exp_member_table tbody tr').length),
      success: function(html)
      {
        $('#exp_member_table tbody').append(html);
        $.fn.catalogue_people.reorder($('#exp_member_table'));
      }
    });
    return true;
  }

*/
   //$("#sub_form_<?php echo $mAction;?>").catalogue_people(/*{add_button: 'a.add_member',update_row_fct: addMember }*/);

});

</script>
