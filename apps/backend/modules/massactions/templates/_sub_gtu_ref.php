  <table>
      <tbody>
      <tr>
          <th><label><?php echo __('Sampling location code');?></label></th>
          <td id="gtu_ref_code"></td>
      </tr>
      <tr>
          <th><label><?php echo __('Latitude');?></label></th>
          <td id="gtu_ref_lat"></td>
      </tr>
      <tr>
          <th><label><?php echo __('Longitude');?></label></th>
          <td id="gtu_ref_lon"></td>
      </tr>
      <tr>
          <th><label><?php echo __('Date from');?></label></th>
          <td id="gtu_date_from"></td>
      </tr>
      <tr>
          <th><label><?php echo __('Date to');?></label></th>
          <td id="gtu_date_to"></td>
      </tr>
      <tr>
          <th class="top_aligned">
              <?php echo $form['MassActionForm']['gtu_ref']['gtu_ref']->renderLabel();?>
          </th>
          <td>
              <?php echo $form['MassActionForm']['gtu_ref']['gtu_ref']->render();?>
          </td>
      </tr>
      </tbody>
  </table>

  <script language="javascript" type="text/javascript">
      $(document).ready(function () {

          function splitGtu()
          {
              el_name = $("#mass_action_MassActionForm_gtu_ref_gtu_ref_name .code");
              if(el_name.length)
              {
                  $("#gtu_ref_code").html($("#mass_action_MassActionForm_gtu_ref_gtu_ref_name .code").html());
                  $("#gtu_ref_lat").html($("#mass_action_MassActionForm_gtu_ref_gtu_ref_name .lat").html());
                  $("#gtu_ref_long").html($("#mass_action_MassActionForm_gtu_ref_gtu_ref_name .lon").html());
                  $("#gtu_date_from").html($("#mass_action_MassActionForm_gtu_ref_gtu_ref_name .date_from").html());
                  $("#gtu_date_to").html($("#mass_action_MassActionForm_gtu_ref_gtu_ref_name .date_to").html());
                  $("#mass_action_MassActionForm_gtu_ref_gtu_ref_name .code").remove();
                  $("#mass_action_MassActionForm_gtu_ref_gtu_ref_name .lat").remove();
                  $("#mass_action_MassActionForm_gtu_ref_gtu_ref_name .lon").remove();
                  $("#mass_action_MassActionForm_gtu_ref_gtu_ref_name .img").remove();
                  $("#mass_action_MassActionForm_gtu_ref_gtu_ref_name .date_from").remove();
                  $("#mass_action_MassActionForm_gtu_ref_gtu_ref_name .date_to").remove();
              }
          }
          $('#mass_action_MassActionForm_gtu_ref_gtu_ref').change(function()
          {
              $("#mass_action_MassActionForm_gtu_ref_gtu_ref_name").html(trim(ref_element_name));
              splitGtu();
          });

          $('#refGtu .ref_clear').click(function()
          {
              $("#gtu_ref_code").html('');
              $("#gtu_ref_lat").html('');
              $("#gtu_ref_lon").html('');
              $("#gtu_date_from").html('');
              $("#gtu_date_to").html('');

          });
          splitGtu();
          changeSubmit(true);

      });
  </script>
