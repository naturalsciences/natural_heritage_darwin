<table>
  <tbody>
    <?php if($form['gtu_from_date']->hasError() || $form['gtu_to_date']->hasError()):?>
      <tr>
        <td colspan="2">
          <?php echo $form['gtu_from_date']->renderError(); ?>
          <?php echo $form['gtu_to_date']->renderError(); ?>
        <td>
      </tr>
    <?php endif; ?>
    <tr>
      <th>
        <?php echo $form['gtu_from_date']->renderLabel(); ?>
      </th>
      <td>
        <?php echo $form['gtu_from_date']->render(); ?>
      </td>
    </tr>
    <tr>
      <th>
        <?php echo $form['gtu_to_date']->renderLabel() ?>
      </th>
      <td>
        <?php echo $form['gtu_to_date']->render() ?>
      </td>
    </tr>
  </tbody>
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
      <th><label><?php echo __('Sampling location code');?></label><?php echo link_to(__('Go to'), url_for("gtu/edit"), array('target' => '_new', 'class'=>'hidden', 'id'=>'gtu_goto_link')) ; ?></th>
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
      </td>
    </tr>
    <tr>
      <td colspan="2" id="specimen_gtu_ref_map"></td>
    </tr>
  </tbody>
</table>

<script language="javascript" type="text/javascript"> 
$(document).ready(function () {
	
	//ftheeten 2018 12 01
	var mask_from=0;
    function adaptCollectingDateFrom_core(ctrl, mode, mask, year, month, day, hour, minute, second)
	{
		//console.log(year);
		if((mask&32)==32)
		{
			//console.log("go");
			$(ctrl+'year option[value="' + year + '"]').prop("selected", "selected");
		}
		if((mask&16)==16)
		{
			//console.log("go");
			$(ctrl+'month option[value="' + month + '"]').prop("selected", "selected");
		}
		if((mask&8)==8)
		{
			//console.log("go");
			$(ctrl+'day option[value="' + day + '"]').prop("selected", "selected");
		}
		if((mask&4)==4)
		{
			//console.log("go");
			$(ctrl+'hour option[value="' + hour + '"]').prop("selected", "selected");
		}
		if((mask&2)==2)
		{
			//console.log("go");
			$(ctrl+'minute option[value="' + minute + '"]').prop("selected", "selected");
		}
		if((mask&1)==1)
		{
			//console.log("go");
			$(ctrl+'second option[value="' + second + '"]').prop("selected", "selected");
		}
		if(mode=="to"&&mask==0)
		{
			$(ctrl+'year option[value=""]').prop("selected", "selected");
			$(ctrl+'month option[value=""]').prop("selected", "selected");
			$(ctrl+'day option[value=""]').prop("selected", "selected");
			$(ctrl+'hour option[value=""]').prop("selected", "selected");
			$(ctrl+'minute option[value=""]').prop("selected", "selected");
			$(ctrl+'second option[value=""]').prop("selected", "selected");
		}
		
	}
	
	function adaptCollectingDateFrom(mode, mask, year, month, day, hour, minute, second)
	{
		/*
		'year' => 32,
		'month' => 16,
		'day' => 8,
		'hour' => 4,
		'minute' => 2,
		'second' => 1,
		*/
		var ctrl='';
		if(mode=="from")
		{
			ctrl='#specimen_gtu_from_date_';
			mask_from=mask;
		}
		else if(mode=="to")
		{
			ctrl='#specimen_gtu_to_date_';
		}
		
       
        adaptCollectingDateFrom_core(ctrl, mode, mask, year, month, day, hour, minute, second);
	}
	
	$(".from_date").change(
		function ()
		{
			//console.log("Change date");
			$("#specimen_gtu_date_from").html("date_set_by_user");
			
		}
	);
	
	$(".to_date").change(
		function ()
		{
			//console.log("Change date");
			$("#specimen_gtu_date_to").html("date_set_by_user");
			
		}
	);
    
    function splitGtu()
    {
	  
          el_name = $("#specimen_gtu_ref_name .code");
          
          if(el_name.length)
          {
            var url = '#';
            if ( $('#specimen_gtu_ref').val() != '' ) {
              url = $("a#gtu_goto_link").attr('href')+'/id/'+$('#specimen_gtu_ref').val();
            }
            //console.log($("#specimen_gtu_ref_name").html());
            adaptCollectingDateFrom("from", $("#specimen_gtu_ref_name .date_from_mask").html(),$("#specimen_gtu_ref_name .date_from_year").html(),$("#specimen_gtu_ref_name .date_from_month").html(), $("#specimen_gtu_ref_name .date_from_day").html(), $("#specimen_gtu_ref_name .date_from_hour").html(), $("#specimen_gtu_ref_name .date_from_minute").html(), $("#specimen_gtu_ref_name .date_from_second").html());
            adaptCollectingDateFrom("to", $("#specimen_gtu_ref_name .date_to_mask").html(),$("#specimen_gtu_ref_name .date_to_year").html(),$("#specimen_gtu_ref_name .date_to_month").html(), $("#specimen_gtu_ref_name .date_to_day").html(), $("#specimen_gtu_ref_name .date_to_hour").html(), $("#specimen_gtu_ref_name .date_to_minute").html(), $("#specimen_gtu_ref_name .date_to_second").html());
            $("#specimen_gtu_ref_code").html("<a href=\""+url+"\" target=\"_new\">"+$("#specimen_gtu_ref_name .code").html()+"</a>");
            $("#specimen_gtu_ref_map").html($("#specimen_gtu_ref_name .img").html());
            $("#specimen_gtu_ref_lat").html($("#specimen_gtu_ref_name .lat").html());
            $("#specimen_gtu_ref_lon").html($("#specimen_gtu_ref_name .lon").html());
            $("#specimen_gtu_date_from").html($("#specimen_gtu_ref_name .date_from").html());
            $("#specimen_gtu_date_to").html($("#specimen_gtu_ref_name .date_to").html());
            
            //$("#specimen_gtu_ref_name .ref_name").remove();
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
