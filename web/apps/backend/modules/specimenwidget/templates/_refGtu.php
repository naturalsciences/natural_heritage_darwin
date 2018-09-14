<table>
  <tbody>
    <?php if($form['gtu_ref']->hasError()):?>
      <tr>
        <td colspan="2"><?php echo $form['gtu_ref']->renderError(); ?><td>
      </tr>
    <?php endif; ?>
    <?php if($form['station_visible']->hasError()):?>
      <tr>
        <td colspan="2"><?php echo $form['station_visible']->renderError(); ?><td>
      </tr>
    <?php endif; ?>
    <tr>
      <th>
        <?php echo $form['station_visible']->renderLabel() ?>
      </th>
      <td>
        <?php echo $form['station_visible']->render() ?>
      </td>
    </tr>
    <tr>
      <th><label><?php echo __('Sampling location code');?></label></th>
      <td id="specimen_gtu_ref_code"></td>
    </tr>
    <tr>
      <th><label><?php echo __('Latitude');?></label></th>
      <td id="specimen_gtu_ref_lat"></td>
    </tr>
    <tr>
      <th><label><?php echo __('Longitude');?></label></th>
      <td id="specimen_gtu_ref_lon"></td>
    </tr>
    <tr>
      <th><label><?php echo __('Date from');?></label></th>
      <td id="specimen_gtu_date_from" class="datesNum"></td>
    </tr>
    <tr>
      <th><label><?php echo __('Date to');?></label></th>
      <td id="specimen_gtu_date_to" class="datesNum"></td>
    </tr>
    <tr>
      <th class="top_aligned">
        <?php echo $form['gtu_ref']->renderLabel() ?>
      </th>
      <td>
        <?php echo $form['gtu_ref']->render() ?>
        <div class="check_right form_buttons">
          <?php echo link_to(__('View'), url_for("gtu/edit?id=".$form['gtu_ref']->getValue()), array('target' => '_new')) ; ?>
        </div>
      </td>
    </tr>
    <tr>
      <td colspan="2" id="specimen_gtu_ref_map">widget</td>
    </tr>
  </tbody>
</table>

<script language="javascript" type="text/javascript"> 
$(document).ready(function () {
    
    function splitGtu()
    {
      el_name = $("#specimen_gtu_ref_name .code");
      if(el_name.length)
      {
		//ftheeten 2016 03 15
		 <?php if($sf_user->isAtLeast(Users::ENCODER)):?>
          <?php echo "$('#specimen_gtu_ref_code').html('".link_to(__('View'), 'gtu/edit?id='.$form['gtu_ref']->getValue(), array('class'=>'view_loc_code', 'target'=>'_blank'))."');"; ?>
		   <?php echo '$(".view_loc_code").text($("#specimen_gtu_ref_name .code").text());';?>
        <?php else:?>
          <?php echo '$("#specimen_gtu_ref_code").html($("#specimen_gtu_ref_name .code").html());';?>
        <?php endif;?>
        //$("#specimen_gtu_ref_code").html($("#specimen_gtu_ref_name .code").html());
        $("#specimen_gtu_ref_map").html($("#specimen_gtu_ref_name .img").html());
        $("#specimen_gtu_ref_lat").html($("#specimen_gtu_ref_name .lat").html());
        $("#specimen_gtu_ref_lon").html($("#specimen_gtu_ref_name .lon").html());
        $("#specimen_gtu_date_from").html($("#specimen_gtu_ref_name .date_from").html());
        $("#specimen_gtu_date_to").html($("#specimen_gtu_ref_name .date_to").html());
        $("#specimen_gtu_ref_name .code").remove();
        $("#specimen_gtu_ref_name .lat").remove();
        $("#specimen_gtu_ref_name .lon").remove();
        $("#specimen_gtu_ref_name .img").remove();
        $("#specimen_gtu_ref_name .date_from").remove();
        $("#specimen_gtu_ref_name .date_to").remove();
      }
    }
    $('#specimen_gtu_ref').change(function()
    {
      $("#specimen_gtu_ref_name").html(trim(ref_element_name));
      splitGtu();
    });

    $('#refGtu .ref_clear').click(function()
    {
      $("#specimen_gtu_ref_code").html('');
        $("#specimen_gtu_ref_map").html('');
        $("#specimen_gtu_ref_lat").html('');
        $("#specimen_gtu_ref_lon").html('');
        $("#specimen_gtu_date_from").html('');
        $("#specimen_gtu_date_to").html('');

    });
    splitGtu();

});
</script>
