<script language="JavaScript" type="text/javascript" src="<?php print(public_path('/openlayers/v5.2.0-dist/ol.js'));?>"></script>
<link rel="stylesheet" href="<?php print(public_path('/openlayers/v5.2.0-dist/ol.css'));?>">
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

  </tbody>
</table>
<table>
    <tr>
        <td colspan="2" id="specimen_gtu_ref_map_ol" >
		<div style="text-align: left">
			
			<div>
					<div style="width: 600px; height:400px" id="map">
							  
					</div>
					<div id="mouse-position"></div>    
				</div>  
				<select id="layer-select" >
                       <option value="Aerial">Aerial</option>
                       <option value="AerialWithLabels" selected>Aerial with labels</option>
                       <option value="Road">Road (static)</option>
                       <option value="RoadOnDemand">Road (dynamic)</option>
					   <option value="OSM">OpenStreetMap</option>
				</select>	
			
		<div>
      </td>
    </tr>
</table>

<script language="javascript" type="text/javascript"> 
	var mousePositionControl;
    var scaleLineControl;
    var map;
	var featuresPoint = new Array();
	var OSM_layer;
		
	var $gtu_ref_code = "";
	GetNagoyaDateSampling();
	$(document).ready(function () {
		$gtu_ref_code = $("#specimen_gtu_ref_code").html();
	
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
		
		$("#specimen_gtu_from_date_year").change(function(){
			GetNagoyaDateSampling();
			setTimeout(function (){ 
				fillcheckandlabels(1);} //in _nagoya.php
			,500); 
		});
		
		$("#specimen_gtu_to_date_year").change(function(){
			GetNagoyaDateSampling();
			setTimeout(function (){ 
				fillcheckandlabels(1);} //in _nagoya.php
			,500); 
		});
		
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
				
				$('#map').html('');
				if($.isNumeric($("#specimen_gtu_ref_lon").html()) && $.isNumeric($("#specimen_gtu_ref_lon").html()))
				{
					init_ol_map($('#specimen_gtu_ref').val(), $("#specimen_gtu_ref_lon").html(),$("#specimen_gtu_ref_lat").html());
				    $('#specimen_gtu_ref_map_ol').show();
				}
				else
				{
					$('#specimen_gtu_ref_map_ol').hide();
				}
			  }
			
		}
		$('#specimen_gtu_ref').change(function()
		{
		 
			$("#specimen_gtu_ref_name").html(trim(ref_element_name));
			splitGtu();
			GetNagoyaGTU();
			setTimeout(function (){ 
				fillcheckandlabels(1);}		//in _nagoya.php
			,500); 
		});

		$('#refGtu .ref_clear').click(function(){
			$("#specimen_gtu_ref_code").html('');
			$("#specimen_gtu_ref_map").html('');
			$("#specimen_gtu_ref_lat").html('');
			$("#specimen_gtu_ref_lon").html('');
			$("#specimen_gtu_date_from").html('');
			$("#specimen_gtu_date_to").html('');

		});
		splitGtu();
	});
	function GetNagoyaGTU(){
		if ($("#specimen_gtu_ref_code").html() !== $gtu_ref_code || $gtu_ref_code == "") {
			var url=location.protocol + '//' + "<?php print(parse_url(sfContext::getInstance()->getRequest()->getUri(),PHP_URL_HOST ));?>" + "/backend.php/specimen/getNagoyaGTU";
			$.getJSON( 
				url,
				{id: $('#specimen_gtu_ref').val()},
				function(data) {
					if(data.nagoya == "yes"){
						$('#gtu').val("ok");
					}else if(data.nagoya == "no"){
						$('#gtu').val("nok");
					}else{
						$('#gtu').val("");
					}
				}
			);
		}
	}
	
	function GetNagoyaDateSampling(){
		var datefrom = new Date( $("#specimen_gtu_from_date_year").val(),$("#specimen_gtu_from_date_month").val()-1,$("#specimen_gtu_from_date_day").val());
		var dateto = new Date( $("#specimen_gtu_to_date_year").val(),$("#specimen_gtu_to_date_month").val()-1,$("#specimen_gtu_to_date_day").val());
		var datenagoya = new Date(2014,9,12);
		var dnull = new Date(1899,10,30);
		
		if(datefrom > datenagoya | dateto > datenagoya){
			$('#date_sampl').val("ok");
		}else{
			$('#date_sampl').val("nok");
		}
		if(dateto.getTime() === dnull.getTime() & datefrom.getTime() === dnull.getTime()){
			$('#date_sampl').val("");
		}
	}
	
	function init_ol_map(gtu_ref, lon,lat)
	{
	

		mousePositionControl= new ol.control.MousePosition({
			 coordinateFormat: ol.coordinate.createStringXY(4),
			projection:'EPSPG:4326',
			className: "custom-mouse-position",
			target: document.getElementById("mouse-position"),
			undefinedHTML: "&nbsp;"
		});
		scaleLineControl = new ol.control.ScaleLine();
			
		

		styleLine=  new ol.style.Style({
          image: new ol.style.Circle({
            radius: 5,
            fill: new ol.style.Fill({color: '#ffff00'}),
            stroke: new ol.style.Stroke({color: '#000000', width: 1})
          })
        });
		
		
	  
		var styles = [
			'Road',
			'RoadOnDemand',
			'Aerial',
			'AerialWithLabels'
		  ];
		var layers = [];
		var i, ii;
		for (i = 0, ii = styles.length; i < ii; ++i) {
			layers.push(new ol.layer.Tile({
			  visible: false,
			  preload: Infinity,
			  source: new ol.source.BingMaps({
				key: " Al7loRcflCy8zRE2HskZKe4cQfzbiMu_kUEUaxjlQNH6DbLHfSqRC2O0_L2ibekX",
				imagerySet: styles[i]
				// use maxZoom 19 to see stretched tiles instead of the BingMaps
				// "no photos at this zoom level" tiles
				// maxZoom: 19
			  })
			}));
		}
	   OSM_layer = new ol.layer.Tile({
		    visible: false,
            source: new ol.source.OSM()
          });
		
			 
		 var wkt = 'POINT('+lon+' '+lat+')';

      var format = new ol.format.WKT();

      var feature = format.readFeature(wkt, {
        dataProjection: 'EPSG:4326',
        featureProjection: 'EPSG:3857'
      });

      var layer_point = new ol.layer.Vector({
        source: new ol.source.Vector({
          features: [feature]
        }),
		style : styleLine
      });

		  
		//layers[layers.length]=layer_point;

       		map = new ol.Map({
				target: 'map',
				layers: layers,    
				 
				view: new ol.View({                    
				  center: ol.proj.transform([parseFloat(lon), parseFloat(lat)], 'EPSG:4326','EPSG:3857'),
				  zoom: 7
				}),
				controls: ol.control.defaults({
						attributionOptions: ({collapsible: false})
				}).extend([mousePositionControl, scaleLineControl])
		});

        mousePositionControl.setProjection("EPSG:4326");
       
	   map.addLayer(OSM_layer);
       map.addLayer(layer_point);
	  
                
        //select background
      var select = document.getElementById('layer-select');
		function onChange() {

			if(select.value!="OSM")
			{
				OSM_layer.setVisible(false);
				var style = select.value;
				for (var i = 0, ii = layers.length; i < ii; ++i) {
				  layers[i].setVisible(styles[i] === style);
				}
			}
			else
			{

				for (var i = 0, ii = layers.length; i < ii; ++i) {
				  layers[i].setVisible(false);
				}
				OSM_layer.setVisible(true);
			}
		}
		select.addEventListener('change', onChange);
		onChange();   
        

		
	}
		
</script>
