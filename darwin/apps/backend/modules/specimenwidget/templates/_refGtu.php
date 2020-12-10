<script language="JavaScript" type="text/javascript" src="<?php print(public_path('/openlayers/v4.x.x-dist/ol.js'));?>"></script>
<link rel="stylesheet" href="<?php print(public_path('/openlayers/v4.x.x-dist/ol.css'));?>">
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
          <?php echo link_to(__('View'), url_for("gtu/edit?id=".$form['gtu_ref']->getValue()), array('target' => '_blank')) ; ?>
        </div>
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
	var $gtu_ref_code = "";
	$(document).ready(function () {
		$gtu_ref_code = $("#specimen_gtu_ref_code").html();
		
		function splitGtu()	{
		  el_name = $("#specimen_gtu_ref_name .code");
		  if(el_name.length) {
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
		$('#specimen_gtu_ref').change(function(){
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
				key: " <?php print(sfConfig::get('dw_bing_key'));?>",
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
