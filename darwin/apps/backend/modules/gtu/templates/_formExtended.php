<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<script language="JavaScript" type="text/javascript" src="<?php print(public_path('/openlayers/v5.2.0-dist/ol.js'));?>"></script>
<link rel="stylesheet" href="<?php print(public_path('/openlayers/v5.2.0-dist/ol.css'));?>">

<!--[if lte IE 8]>
    <link rel="stylesheet" href="/leaflet/leaflet.ie.css" />
<![endif]-->

<script type="text/javascript">
$(document).ready(function () 
{
  $('body').catalogue({});
});
</script>
<style>

      .draw-point {
        top: 65px;
        left: .5em;
        width: 1.375em;
        height: 1.375em; 
        background-color: rgba(255,255,255,.4); 
        text-align: center; 
           
      }
	  
      .draw-box {
        top: 100px;
        left: .5em;
        width: 1.375em;
        height: 1.375em; 
        background-color: rgba(255,255,255,.4); 
        text-align: center; 
           
      }
	  
	  
      
      .draw-polygon {
        top: 135px;
        left: .5em;
        width: 1.375em;
        height: 1.375em; 
        background-color: rgba(255,255,255,.4); 
        text-align: center; 
           
      }
      
	  .draw-line {
        top: 170px;
        left: .5em;
        width: 1.375em;
        height: 1.375em; 
        background-color: rgba(255,255,255,.4); 
        text-align: center; 
           
      }
      .delete-map {
        top: 205px;
        left: .5em;
        width: 1.375em;
        height: 1.375em; 
        background-color: rgba(255,255,255,.4); 
        text-align: center; 
           
      }
	  
      .move-map {
        top: 240px;
        left: .5em;
        width: 1.375em;
        height: 1.375em; 
        background-color: rgba(255,255,255,.4); 
        text-align: center; 
           
      }
	  
	  
	  
	  .map:-moz-full-screen {
        height: 100%;
      }
      .map:-webkit-full-screen {
        height: 100%;
      }
      .map:-ms-fullscreen {
        height: 100%;
      }
      .map:fullscreen {
        height: 100%;
      }
      .ol-rotate {
        top: 3em;
      }
	  
     
     
      
    </style>
<?php echo form_tag('gtu/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()), array('class'=>'edition'));?>
<input type="hidden" name="rich" id="rich" value="on"/>
<?php if (!$form->getObject()->isNew()): ?>
	<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
<table>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th class="top_aligned"><?php echo $form['code']->renderLabel() ?></th>
        <td>
          <?php echo $form['code']->renderError() ?>
          <?php echo $form['code'] ?>
        </td>
      </tr>
    </tbody>
</table>
<!--JMHerpers 2019 05 29-->
<table style="margin-top:20px; margin-bottom: 20px">
	<tr>
        <th><?php echo $form['nagoya']->renderLabel() ?></th>
        <td>
			<?php echo $form['nagoya']->renderError() ?>
			<?php echo $form['nagoya'] ?>
			
			<a href=location.protocol + '//' + location.host + "/"+ location.pathname.split("/")[1] + "/"+ location.pathname.split("/")[2] +"/help/nagoya_countries.html" target="popup" onclick="window.open(location.protocol + '//' + '<?php print(parse_url(sfContext::getInstance()->getRequest()->getUri(),PHP_URL_HOST ));?>' +'/help/nagoya_countries.html','popup','width=1150,height=800'); return false;" style="display: inline-block;">
				<?php echo image_tag('info.png',"title=nagoya_info class=nagoya_info id=nagoya");?>
			</a>
        </td>
	</tr>
</table>

<?php
$tag_grouped = array();
$avail_groups = TagGroups::getGroups(); 
foreach($form['TagGroups'] as $group)
{
  $type = $group['group_name']->getValue();
  if(!isset($tag_grouped[$type]))
    $tag_grouped[$type] = array();
  $tag_grouped[$type][] = $group;
}
foreach($form['newVal'] as $group)
{
  $type = $group['group_name']->getValue();
  if(!isset($tag_grouped[$type]))
    $tag_grouped[$type] = array();
  $tag_grouped[$type][] = $group;
}
?>
<div id="gtu_group_screen">
<div class="tag_parts_screen" alt="<?php echo url_for('gtu/addGroup'. ($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>">
<?php foreach($tag_grouped as  $group_key => $sub_forms):?>
  <fieldset alt="<?php echo $group_key;?>">
    <legend><?php echo __($avail_groups[$group_key]);?></legend>
    <ul>
      <?php foreach($sub_forms as $form_value):?>
	<?php include_partial('taggroups', array('form' => $form_value));?>
      <?php endforeach;?>
    </ul>
    <a class="sub_group"><?php echo __('Add Sub Group');?></a>
  </fieldset>
<?php endforeach;?>
</div>


  <div class="gtu_groups_add">
    <select id="groups_select">
      <option value=""></option>
      <?php foreach(TagGroups::getGroups() as $k => $v):?>
        <option value="<?php echo $k;?>"><?php echo $v;?></option>
      <?php endforeach;?>
    </select>
    <a href="<?php echo url_for('gtu/addGroup'. ($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>" id="add_group"><?php echo __('Add Group');?></a>
  </div>

</div>
    
  <fieldset id="location">
    <legend><?php echo __('Localisation');?></legend>
    <div id="reverse_tags" style="display: none;"><ul></ul><br class="clear" /></div>
    <div>
		<!--DMS/DD selector  ftheeten 2015 05 05-->
		<b><?php echo $form['coordinates_source']->renderLabel() ;?><?php echo $form['coordinates_source']->renderError() ?></b><br/><?php echo $form['coordinates_source'];?>
		
	</div>
    <table>
		<tr>
			<td colspan="4">
				<div class="GroupDMS" style="display: None">
					<table >
						<!--DMS columns ftheeten 2015 05 05-->
						<!--<tr >
							<th ><?php echo $form['latitude_dms_degree']->renderLabel() ;?><?php echo $form['latitude_dms_degree']->renderError() ?></th>
							<th ><?php echo $form['latitude_dms_minutes']->renderLabel(); ?><?php echo $form['latitude_dms_minutes']->renderError() ?></th>
							<th ><?php echo $form['latitude_dms_seconds']->renderLabel(); ?><?php echo $form['latitude_dms_seconds']->renderError() ?></th>
							<th ><?php echo $form['latitude_dms_direction']->renderLabel(); ?><?php echo $form['latitude_dms_direction']->renderError() ?></th>
						</tr>-->
						<tr >
							<th ><?php echo 'Latitude';?></th>
							<th />
							<th />
							<th />
						</tr>
						<tr >
							<td ><?php echo 'Degrees: '.$form['latitude_dms_degree'];?></td>
							<td ><?php echo 'Minutes: '.$form['latitude_dms_minutes'];?></td>
							<td ><?php echo 'Seconds: '.$form['latitude_dms_seconds'];?></td>
							<td ><?php echo 'Direction: '.$form['latitude_dms_direction'];?></td>
						</tr>
						<!--<tr >
							<th ><?php echo $form['longitude_dms_degree']->renderLabel() ;?><?php echo $form['longitude_dms_degree']->renderError() ?></th>
							<th ><?php echo $form['longitude_dms_minutes']->renderLabel(); ?><?php echo $form['longitude_dms_minutes']->renderError() ?></th>
							<th class="GroupDMS"><?php echo $form['longitude_dms_seconds']->renderLabel(); ?><?php echo $form['longitude_dms_seconds']->renderError() ?></th>
							<th class="GroupDMS"><?php echo $form['longitude_dms_direction']->renderLabel(); ?><?php echo $form['longitude_dms_direction']->renderError() ?></th>
						</tr>-->
						<tr >
							<th ><?php echo 'Longitude';?></th>
							<th />
							<th />
							<th />
						</tr>
						<tr >
							<td><?php echo 'Degrees: '.$form['longitude_dms_degree'];?></td>
							<td><?php echo 'Minutes: '.$form['longitude_dms_minutes'];?></td>
							<td><?php echo 'Seconds: '.$form['longitude_dms_seconds'];?></td>
							<td><?php echo 'Direction: '.$form['longitude_dms_direction'];?></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<div class="GroupUTM" style="display: None">
					<table>
						<!--DMS columns ftheeten 2015 05 05-->
						<tr>
							<th><?php echo $form['latitude_utm']->renderLabel() ;?><?php echo $form['latitude_utm']->renderError() ?></th>
							<th><?php echo $form['longitude_utm']->renderLabel(); ?><?php echo $form['longitude_utm']->renderError() ?></th>
							<th><?php echo $form['utm_zone']->renderLabel(); ?><?php echo $form['utm_zone']->renderError() ?></th>
						</tr>
						<tr>
							<td><?php echo $form['latitude_utm'];?></td>
							<td><?php echo $form['longitude_utm'];?></td>
							<td><?php echo $form['utm_zone'];?></td>
							
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<th class="GroupDD" style="display: None"><?php echo $form['latitude']->renderLabel() ;?><?php echo $form['latitude']->renderError() ?></th>
			<th class="GroupDD" style="display: None"><?php echo $form['longitude']->renderLabel(); ?><?php echo $form['longitude']->renderError() ?></th>
			<th><?php echo $form['lat_long_accuracy']->renderLabel() ;?><?php echo $form['lat_long_accuracy']->renderError() ?></th>
			<th></th>
		</tr>
		<tr>
			<td class="GroupDD" style="display: None"><?php echo $form['latitude'];?></td>
			<td class="GroupDD" style="display: None"><?php echo $form['longitude'];?></td>
			<td><?php echo $form['lat_long_accuracy'];?></td>
			<td><strong><?php echo __('m');?></strong><!-- <?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?>--></td>
			<td></td>
		</tr>
      <tr>        
        <th><?php echo $form['elevation']->renderLabel(); ?><?php echo $form['elevation']->renderError() ?></th>
        <th><?php echo $form['elevation_accuracy']->renderLabel() ;?><?php echo $form['elevation_accuracy']->renderError() ?></th>
        <th></th>
      </tr>
      <tr>        
        <td><?php echo $form['elevation'];?></td>
        <td><?php echo $form['elevation_accuracy'];?></td>
        <td><strong><?php echo __('m');?></strong> <?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?></td>
      </tr>
      <tr>
        <td colspan="3">
		<div>
			<select id="addwms" class="form-control">
				<?php foreach(sfConfig::get('dw_wfs_layers') as $url=>$name):?>
					<option value="<?php print($url);?>"><?php print($name);?></option>
				<?php endforeach;?>				  
			</select>
			<input id="browse_wms" type="button" value="Browse layers"></input>		
			<select id="addwmslayer" class="form-control">
				
			</select>
			<input id="put_layer" type="button" value="Add layers"></input>
			
			<br/>
			Selected layers :
			<input type="text" id="chosen_layer" name="chosen_layer" style="width:70%" readonly>
			 <input id="remove_last" type="button" value="Remove last"></input>	
			<table> 			
				<tr>
					<td><?php echo $form['geom_wkt']->renderLabel();?></td><td><?php echo $form['geom_wkt']->render();?></td>
				</tr>
				<tr>
					<td>WFS data</td><td><input type="text" name="geom_wfs" id="geom_wfs" class="wfs_search"><input type="hidden" name="wfs_json" id="wfs_json" class="wfs_json"></td>
				</tr>
				<tr>
					<td>Select WFS item</td><td><input type="checkbox" name="select_wfs_item" id="select_wfs_item"/></td>
				</tr>
				<tr>
					<td><?php echo $form['georeference_ref']->render();?></td>
				</tr>
			</table>
			
			<div style="width:700px; height:700px; display:inline-block;" id="map"></div>
			<br/> <div id="mouse-position"></div> 
			
		</div>	
		</td>
	</tr>
	
	<tr>
	  <td>
	  <select id="layer-select" >
                       <option value="Aerial">Aerial</option>
                       <option value="AerialWithLabels" selected >Aerial with labels</option>
                       <option value="Road">Road (static)</option>
                       <option value="RoadOnDemand">Road (dynamic)</option>
					   <option value="OSM"  >OpenStreetMap</option>
				</select>	
	  </td>
	  <td> 
	  <div class="clean_map"><?php print(image_tag('remove.png', array("class"=>"clean_map", "title"=>"Clean map")));?></div>
	  </td>
        <td>

<script type="text/javascript">

	var mousePositionControl;
		var scaleLineControl;
		var map;
		var featuresPoint = new Array();
		var OSM_layer;
		var loaded=false;
		var layer_point;
		var iLayer=0;
		var vectorLoaded =false;
		var type_draw="";
		var source_draw = new ol.source.Vector({wrapX: false});
		var draw;
		var mode_area=false;
		var mode_wfs=false;
		var mode_point=true;
		var is_drawing=true;
		var globalLayers=Array();
		var WFSArray=Array();
		var LayerArray=Array();
		var current_wms;
		var current_layer_name;
		var layerJSON;
		var layerJSON_loaded=false;
		
    function init_georef(tmp_ref)
    {    
	    var url_service= detect_https('<?php echo url_for("gtu/getGeorefServiceJSON");?>');
		var request = $.ajax({
              url: url_service,
              method: "GET",
              data: {id:tmp_ref},
              dataType: "json"
            }).done(
                function(result)
                {
                 
				 LayerArray.push("TMP");
				 WFSArray.push("TMP");
				 
				  removeDarwinLayer(iLayer);
				   addDarwinLayerWFS(result);
				   map.getView().fit( layerJSON.getSource().getExtent(), map.getSize());
                }
            );
	}	
		var wfs_url="<?php print(sfConfig::get('dw_root_url_wfs'));?>";
		
		   	var styleWKT= new ol.style.Style({
			  fill: new ol.style.Fill({
				color: 'rgba(255, 255, 255, 0.2)'
			  }),
			  stroke: new ol.style.Stroke({
				color: '#ffcc33',
				width: 4
			  }),
			  image: new ol.style.Circle({
				radius: 7,
				fill: new ol.style.Fill({
				  color: '#ffcc33'
				})
			  })
			});
			
			var styleWKTGreen= new ol.style.Style({
			  fill: new ol.style.Fill({
				color: 'rgba(255, 255, 255, 0.2)'
			  }),
			  stroke: new ol.style.Stroke({
				color: '#009900',
				width: 4
			  }),
			  image: new ol.style.Circle({
				radius: 7,
				fill: new ol.style.Fill({
				  color: '#009900'
				})
			  })
			});
			
			
		var createMultiPolygon=function()
		{
			var returned="";
			if(WFSArray.length>0)
			{
				$('#chosen_layer').val(LayerArray.join("; "));
				returned=JSON.stringify(WFSArray);
				//returned="GEOMETRYCOLLECTION("+ WFSArray.join(",") +")";
			}
			$('.wfs_search').val(returned);
			
			
		}
		var init_point=function(lon, lat)
		{
			var wkt = 'POINT('+lon+' '+lat+')';
				var format = new ol.format.WKT();
				var feature = format.readFeature(wkt, {
				dataProjection: 'EPSG:4326',
				featureProjection: 'EPSG:3857'
			  });

			  layer_point = new ol.layer.Vector({
				source: new ol.source.Vector({
				  features: [feature]
				}),
				style : styleLine
			  });
			  loaded=true;
		}
		
		var init_point_direct=function(lon, lat)
		{
			var wkt = 'POINT('+lon+' '+lat+')';
				var format = new ol.format.WKT();
				var feature = format.readFeature(wkt, {
			  });

			  layer_point = new ol.layer.Vector({
				source: new ol.source.Vector({
				  features: [feature]
				}),
				style : styleLine
			  });
			  loaded=true;
		}
		
		var redraw_point=function(lonlat)
			{
				if(loaded)
				{
					map.removeLayer(layer_point);
				}
				init_point(lonlat[0], lonlat[1]);
				map.addLayer(layer_point);
				$("#gtu_longitude").val(lonlat[0]);
				$("#gtu_latitude").val(lonlat[1]);
				
			}
			
		function removeDarwinLayer(p_max){		
			if(vectorLoaded){
				map.getLayers().forEach(function(layer) {	
					if (typeof layer !== 'undefined') {			
						if(layer.get("name")!="background"&&parseInt(layer.get("name"))==p_max ){				
							map.removeLayer(layer);
						}
					}
				});
			}		
		}
		
		function addDarwinLayer(feature,origininput, type_draw)
        {
			if(type_draw=="linestring")
			{
				var tmp_geom =new ol.geom.LineString(feature.getGeometry().getCoordinates());
			}
			else
			{
				var tmp_geom =new ol.geom.Polygon(feature.getGeometry().getCoordinates());
			}
            var  generic_feature = new ol.Feature({geometry: tmp_geom});
              
            var tmpSource=new ol.source.Vector();
            tmpSource.addFeature(generic_feature);
            iLayer++;
            var vectorlayer_local = new ol.layer.Vector({
                        name: iLayer,
                        source: tmpSource,
                        style: styleWKT	});
                        
            
            map.addLayer(vectorlayer_local);
            var format = new ol.format.WKT();
			tmp_geom4326= tmp_geom.clone();
			tmp_geom4326.transform("EPSG:3857", "EPSG:4326");
			wktfeaturegeom = format.writeGeometry(tmp_geom4326);
			$('.wkt_search').val(wktfeaturegeom);
            vectorLoaded=true;	
			removePoint();			
        }
		
		
		function addDarwinLayerWFS(p_data)
        {
            iLayer++;      
			var vectorJSON = new ol.source.Vector({
							features: (new ol.format.GeoJSON({
										defaultDataProjection: 'EPSG:4326',
										featureProjection: 'EPSG:3857'
								})).readFeatures(p_data)
			});
						
			layerJSON = new ol.layer.Vector({
							source: vectorJSON,
							style: styleWKTGreen,
							name:iLayer
						});			
            map.addLayer(layerJSON);
            var format = new ol.format.WKT();
			vectorLoaded=true;	
			removePoint();
			//var parser = new ol.format.GeoJSON();
			//var conv = parser.writeFeatures(vectorJSON.getFeatures(), {featureProjection: 'EPSG:4326'});
			$(".wfs_json").val( JSON.stringify(p_data));						
        }
		
		function removePoint()
		{
			if(loaded)
			{
				map.removeLayer(layer_point);
				$("#gtu_longitude").val("");
				$("#gtu_latitude").val("");
				loaded=false;
			}
		}
		
		function addDarwinLayerWkt(wkt,origininput )
        { 
			type_draw="polygon";
			if(wkt.toLowerCase().includes("linestring"))
			{
				type_draw="linestring";
			}
		    var format = new ol.format.WKT();      
		    var feature = format.readFeature(wkt, {
						dataProjection: 'EPSG:4326',
						featureProjection: 'EPSG:3857'
					  });
            if(type_draw=="linestring")
			{
				var tmp_geom =new ol.geom.LineString(feature.getGeometry().getCoordinates());
			}
			else
			{
				var tmp_geom =new ol.geom.Polygon(feature.getGeometry().getCoordinates());
			}
            var  generic_feature = new ol.Feature({geometry: tmp_geom});
              
            var tmpSource=new ol.source.Vector();
            tmpSource.addFeature(generic_feature);
            iLayer++;
            var vectorlayer_local = new ol.layer.Vector({
                        name: iLayer,
                        source: tmpSource,
                        style: styleWKT	});
                        
            
            map.addLayer(vectorlayer_local);
            var format = new ol.format.WKT();
			tmp_geom4326= tmp_geom.clone();
			tmp_geom4326.transform("EPSG:3857", "EPSG:4326");
			wktfeaturegeom = format.writeGeometry(tmp_geom4326);
			$('.wkt_search').val(wktfeaturegeom);
            vectorLoaded=true;	
			map.getView().fit(vectorlayer_local.getSource().getExtent(), map.getSize());
			removePoint();
			
        }
		
			
		function init_map()
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
			
			
		  <?php if($form->getObject()->getLongitude() != ''):?>
				init_point(<?php echo $form->getObject()->getLongitude();?>,<?php echo $form->getObject()->getLatitude();?>);
				
		   <?php endif;?>				 
		 
		 
				//	button draw point
				  DrawPointControl = function(opt_options) {
							
							
							var options = opt_options || {};
							var element = document.createElement('div');
							element.className = 'draw-point ol-unselectable ol-control';
							element.innerHTML='.';   
							$(element).click(
								function()
								{
									 mode_area=false;
									 mode_wfs=false;
									 mode_point=true;
								}
							);
							 ol.control.Control.call(this, {
							  element: element,
							  target: options.target
							});
				  };
				 ol.inherits(DrawPointControl, ol.control.Control);

		   	     //button draw bbox
				  DrawBoxControl = function(opt_options) {
							
							
							var options = opt_options || {};
							var element = document.createElement('div');
							element.className = 'draw-box ol-unselectable ol-control';
							element.innerHTML='&#9633;';   
							$(element).click(
								function()
								{
									is_drawing=true;
								    mode_area=true;
									mode_wfs=false;
									mode_point=false;
									type_draw="box";
									removeDarwinLayer(iLayer);
									map.removeInteraction(draw);
									draw = new ol.interaction.Draw({
									source: source_draw,
									type: 'circle',
									geometryFunction: ol.interaction.Draw.createBox(),
									//finishCondition: ol.events.condition.doubleClick 
									});
									draw.on('drawend', function (event) {                        
										addDarwinLayer(event.feature,"from drawing", type_draw);
										map.removeInteraction(draw);
									});
									map.addInteraction(draw);
								}
							);
							 ol.control.Control.call(this, {
							  element: element,
							  target: options.target
							});
				  };
				 ol.inherits(DrawBoxControl, ol.control.Control);
				 
				 //button draw Polygons
				  DrawPolygonControl = function(opt_options) {
						   
							var options = opt_options || {};
							var element = document.createElement('div');
							element.className = 'draw-polygon ol-unselectable ol-control';
							element.innerHTML='&#11040;';   
							$(element).click(
								function()
								{
									is_drawing=true;
									mode_area=true;
									mode_wfs=false;
									mode_point=false;
									 type_draw="polygon";
									removeDarwinLayer(iLayer);
									map.removeInteraction(draw);
									draw = new ol.interaction.Draw({
									source: source_draw,
									type: 'Polygon',
									condition: function(e) {
									  // when the point's button is 1(leftclick), allows drawing
									  //right click is for deleting (see context below)
									  if (e.pointerEvent.buttons === 1) { 
										return true;
									  } else {
										return false;
									  }
									}
									});
									draw.on('drawend', function (event) {
										addDarwinLayer(event.feature,"from drawing", type_draw);
										map.removeInteraction(draw);
									});
									map.addInteraction(draw);
								}
							);
							 ol.control.Control.call(this, {
							  element: element,
							  target: options.target
							});
				  };
				 ol.inherits(DrawPolygonControl, ol.control.Control);
				 
				  //button draw Polygons
				  DrawLineControl = function(opt_options) {
						   
							var options = opt_options || {};
							var element = document.createElement('div');
							element.className = 'draw-line ol-unselectable ol-control';
							element.innerHTML='|';   
							$(element).click(
								function()
								{
									is_drawing=true;
									mode_area=true;
									mode_wfs=false;
									mode_point=false;
									 type_draw="linestring";
									removeDarwinLayer(iLayer);
									map.removeInteraction(draw);
									draw = new ol.interaction.Draw({
									source: source_draw,
									type: 'LineString',
									condition: function(e) {
									  // when the point's button is 1(leftclick), allows drawing
									  //right click is for deleting (see context below)
									  if (e.pointerEvent.buttons === 1) { 
										return true;
									  } else {
										return false;
									  }
									}
									});
									draw.on('drawend', function (event) {
										addDarwinLayer(event.feature,"from drawing",type_draw);
										map.removeInteraction(draw);
									});
									map.addInteraction(draw);
								}
							);
							 ol.control.Control.call(this, {
							  element: element,
							  target: options.target
							});
				  };
				 ol.inherits(DrawLineControl, ol.control.Control);
				 
				 
				 DeleteControl = function(opt_options) {
							var options = opt_options || {};
							var element = document.createElement('div');
							element.className = 'delete-map ol-unselectable ol-control';
							element.innerHTML='x';   
							$(element).click(
								function()
								{
									if (typeof draw !== 'undefined') 
									{
										draw.removeLastPoint();
									}
								}
							);
							 ol.control.Control.call(this, {
							  element: element,
							  target: options.target
							});
				  };
				ol.inherits(DeleteControl, ol.control.Control);
				
				  //button moveMap
				   MoveMapControl = function(opt_options) {
							var options = opt_options || {};
							var element = document.createElement('div');
							element.className = 'move-map ol-unselectable ol-control';
							element.innerHTML='&#10021;';   
							$(element).click(
								function()
								{
									removeDarwinLayer(iLayer);
									map.removeInteraction(draw);
									$('.wkt_search').val("");
									
								}
							);
							 ol.control.Control.call(this, {
							  element: element,
							  target: options.target
							});
				  };
				ol.inherits(MoveMapControl, ol.control.Control);

			  
			//layers[layers.length]=layer_point;
		 
				map = new ol.Map({
					target: 'map',
					layers: layers,    
					 
					view: new ol.View({ 
					<?php if($form->getObject()->getLongitude() != ''):?>				
					  center: ol.proj.fromLonLat([<?php print($form->getObject()->getLongitude());?>,<?php print($form->getObject()->getLatitude());?>]),
					 <?php else: ?>
						center: ol.proj.fromLonLat([4.376632,50.8366595]),
					 <?php endif;?>
					  zoom: 7
					}),
					controls: ol.control.defaults({
							attributionOptions: ({collapsible: false})
					}).extend([mousePositionControl, scaleLineControl,new DrawPointControl(), new DrawBoxControl(), new DrawPolygonControl(), new DrawLineControl(), new MoveMapControl(), new DeleteControl()])
			});
			mousePositionControl.setProjection("EPSG:4326");
		   
		   map.addLayer(OSM_layer);
			<?php if($form->getObject()->getLongitude() != ''):?>
			map.addLayer(layer_point);
		  <?php endif;?>		
					
			//select background
		  var select = document.getElementById('layer-select');
			function onChange() {
				console.log(select.value)
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
					console.log("trye");
					for (var i = 0, ii = layers.length; i < ii; ++i) {
					  layers[i].setVisible(false);
					}
					OSM_layer.setVisible(true);
				}
			}
			
		
			
			select.addEventListener('change', onChange);
			onChange();   
			
			map.on('dblclick', function(evt) {            
            if(type_draw=="box")            {
              
                draw.finishDrawing();
				
            }
			is_drawing=false;
			});
			//right click
			map.on('contextmenu', function(evt) {  
				console.log("RIGHT CLICK");
				if(is_drawing)
				{
					console.log("Remove");
					if (typeof draw !== 'undefined') 
					{
						draw.removeLastPoint();
					}
				}
			});

			map.on('click', function(event) {
				if(mode_point)
				{
				
					var lonlat = event.coordinate;
					lonlat= ol.proj.transform(lonlat, "EPSG:3857", "EPSG:4326");
				
					redraw_point(lonlat);
				}	
			});
			
			map.on('singleclick', function(evt) {
			console.log("click");
			if(globalLayers.length>0&&mode_wfs)
			{
				console.log("add_WFS");
				var lonlat = map.getCoordinateFromPixel(evt.pixel);
				lonlat= ol.proj.transform(lonlat, "EPSG:3857", "EPSG:4326");
				//console.log(lonlat);
				var filter="DWITHIN(geom, POINT ("+ lonlat[1] +" "+ lonlat[0] +"), 100, meters)";
				
				var query_url=wfs_url + current_wms+"/wfs?service=wfs&version=2.0.0&request=GetFeature&typeNames="+ current_layer_name +"&buffer=20&outputFormat=application/json&srsName=EPSG:4326&cql_filter="+filter;
				//console.log(query_url);
				$.get( query_url)
				  .done(function( data ) {
						if(data["features"].length>0)
						{
							LayerArray=Array();
							WFSArray=Array();
							
							var gid=data["features"][0]['properties']['gid'];
							var name=data["features"][0]['properties']['name'];
							console.log(gid);
							var geom=data["features"][0]["geometry"]["coordinates"];
							var tmp={root_url:wfs_url + current_wms+"/wfs?" ,layer: current_layer_name ,'value':gid}
							console.log("add");
							LayerArray.push(name);
							WFSArray.push(tmp);
							data["features"][0]['properties']={};
							removeDarwinLayer(iLayer);
console.log(data);				
							
							addDarwinLayerWFS(data["features"][0]["geometry"]);
											
							createMultiPolygon();
						}
					    
				  });
			}
	});		

        

		
	}
		init_map();
		var array_wkt=Array();
		var return_styles =function(color1, color2, color3, vertices)
		{
			return [
					
					new ol.style.Style({
					  stroke: new ol.style.Stroke({
						color: color1,
						width: 3
					  }),
					  fill: new ol.style.Fill({
						color: color2
					  })
					}),
					new ol.style.Style({
					  image: new ol.style.Circle({
						radius: 5,
						fill: new ol.style.Fill({
						  color: color3
						})
					  }),
					  geometry: vertices
					})
				  ];
		}
	var draw_wkt=function(color1, color2, color3)
	{
		if(array_wkt.length>0)
		{
			var feature_list=Array();
			var vertices;
			
				for(var i=0; i<array_wkt.length; i++)
				{
					var format = new ol.format.WKT();         
					
					var geom = format.readGeometry(array_wkt[i]);					
					var coordinates = geom.getCoordinates()[0];
					
					if(i==0)
					{
						vertices=new ol.geom.MultiPoint(coordinates)
					}
					else
					{
						for(var j=0; j<coordinates.length; j++)
						{
							
							vertices.appendPoint(new ol.geom.Point(coordinates[j]));
						}
					}
					  var feature = format.readFeature(array_wkt[i], {
						dataProjection: 'EPSG:4326',
						featureProjection: 'EPSG:3857'
					  });
					  feature_list.push(feature);
				}
	
			
			
			 var p_style=return_styles(color1, color2, color3,vertices);
			  var wkt_layer = new ol.layer.Vector({
				source: new ol.source.Vector({
				  features: feature_list
				}),
				style: p_style
			  });
			map.addLayer(wkt_layer);
		 
			map.getView().fit(wkt_layer.getSource().getExtent(), map.getSize());
		}
	}
<?php $array_georef=$form->getObject()->getGeoreferencesByService(); if($array_georef !==null):?>
 
 <?php foreach($array_georef as $key=>$ref_obj):?>
		array_wkt.push("<?php print($ref_obj->getWkt()); ?>");
		
		
  <?php endforeach;?>

draw_wkt('blue', 'rgba(0, 0, 255, 0.1)', 'orange');
<?php endif;?>
  

	var parseCapabilities= function(wms_point)
	{
		var cap_query=wfs_url + wms_point+ '/ows?service=wms&version=1.1.1&request=GetCapabilities';
		console.log(cap_query);
		$.get( cap_query)
			  .done(function( data ) {
					console.log(data);
					$('#addwmslayer').find('option').remove().end();
					$(data).find("Layer > Name").each(
						function(index, obj)
						{
							var name_layer=obj.childNodes[0].nodeValue;
							//console.log(name_layer);
							var o = new Option(name_layer, name_layer);
							$(o).html(name_layer);
							$("#addwmslayer").append(o);
						}
					);					
			  });
	}
	
	$("#browse_wms").click(
		function()
		{
			var wms_url=$("#addwms").val();					
            parseCapabilities(wms_url);
			current_wms=wms_url;
		}
	);
	
	var addLayer_dw=function(wms_point, layer_name)
	{
			$(globalLayers).each(
				function(idx, obj)
				{
					map.removeLayer(obj);			
					
				}
			);
			globalLayers=Array();
			var wms_layer= new ol.layer.Tile(
				{
					source: new ol.source.TileWMS(
					{
					  url: wfs_url + wms_point + '/ows?',
					  params: {'LAYERS': layer_name},
					  ratio: 1,
					  serverType: 'geoserver',
					  projection: 'EPSG:4326',
					  transition: 0
					}
					)
				}
				);
		    current_layer_name=layer_name;
			globalLayers.push(wms_layer);
			map.addLayer(wms_layer);				
	}
	
	   $("#put_layer").click(
		function()
		{
			var wms_url=$("#addwmslayer").val();					
            addLayer_dw($("#addwms").val(),$("#addwmslayer").val());
			
		}
		
		
	);


        //ftheteen 2016 09 15
        function update_point_on_map( lati, longi, accu)
        {
           if (!isNaN(lati)&&!isNaN(longi)) 
		   {
			if(lati.length>0&&longi.length>0)
			{
				redraw_point([longi, lati]);
			}
			//map.getView().setCenter(ol.proj.transform([longi, lati], 'EPSG:4326', 'EPSG:3857'));
		   }
		}
		
		$("#select_wfs_item").change(
			function()
			{
				if($("#select_wfs_item").is(":checked"))
				{
					mode_wfs=true;
					mode_point=false;
					mode_area=false;
					console.log("check");
				}
				else
				{
					mode_wfs=false;
					console.log("uncheck");
				}
				
			}
		);
</script>
</td>
      </tr>
    </table>

  </fieldset>

  <table>
    <tfoot>
      <tr>
        <td>
          <?php echo $form->renderHiddenFields(true) ?>

          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('New Gtu'), 'gtu/new') ?>
            &nbsp;<?php echo link_to(__('Duplicate Gtu'), 'gtu/new?duplicate_id='.$form->getObject()->getId()) ?>
          <?php endif?>

          &nbsp;<a href="<?php echo url_for('gtu/index') ?>"><?php echo __('Cancel');?></a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to(__('Delete'), 'gtu/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>


<script  type="text/javascript">
    

$(document).ready(function () {


    $('.counter_date').text($('#gtu_temporal_information option').size()+" Value(s)");
    /*$('.add_date').live('click', function(event)
        {
            //document.forms[0].submit();
           //$("#submit").click();
            //$("#submit").click();
          // event.preventDefault();
        }
    );*/
    
     /*$('.remove_date').live('click', function(event)
        {
            
             
            $('#gtu_delete_mode').prop('checked', true);            
            $("#submit").click();
            //document.forms[0].submit();
            event.preventDefault();
        }
    );*/
    
    //ftheeten 2016 02 05

	showDMSCoordinates=false;
    
    $('.tag_parts_screen .clear_prop').live('click', function()
    {
      parent_el = $(this).closest('li');
      $(parent_el).find('input').val('');
      $(parent_el).hide();

      sub_groups  = parent_el.parent();
      if(sub_groups.find("li:visible").length == 0)
      {
	      sub_groups.closest('fieldset').hide();
      	disableUsedGroups();
      }	
         
    });

   

    disableUsedGroups();
    $('.purposed_tags li').live('click', function()
    {
      input_el = $(this).parent().closest('li').find('input[id$="_tag_value"]');
      if(input_el.val().match("\;\s*$"))
        input_el.val( input_el.val() + $(this).text() );
      else
        input_el.val( input_el.val() + " ; " +$(this).text() );
      input_el.trigger('click');
    });

    $('input[id$="_tag_value"]').live('keydown click',purposeTags);

   function purposeTags(event)
   {
      if (event.type == 'keydown')
      {
        var code = (event.keyCode ? event.keyCode : event.which);
        if (code != 59 /* ;*/ && code != $.ui.keyCode.SPACE ) return;
      }
      parent_el = $(this).closest('li');
      group_name = parent_el.find('input[name$="\[group_name\]"]').val();
      sub_group_name = parent_el.find('[name$="\[sub_group_name\]"]').val();
      if(sub_group_name == '' || $(this).val() == '') return;
      $('.purposed_tags').hide();
      $.ajax({
        type: "GET",
        url: "<?php echo url_for('gtu/purposeTag');?>" + '/group_name/' + group_name + '/sub_group_name/' + sub_group_name + '/value/'+ $(this).val(),
        success: function(html)
        {
          parent_el.find('.purposed_tags').html(html);
          parent_el.find('.purposed_tags').show();
        }
      });
    }

    $('#add_group').click(function(event)
    {
      event.preventDefault();
      selected_group = $('#groups_select option:selected').val();
      addGroup(selected_group);
    });

    $('a.sub_group').live('click',function(event)
    {
      event.preventDefault();
      addSubGroup( $(this).closest('fieldset').attr('alt'));
    });
    
        //ftheeten 2016 09 15
    checkCoordSourceState();
	
	if ($('.wkt_search').val() != ''){
		console.log();
		var tmp_wkt=$('.wkt_search').val();
		addDarwinLayerWkt(tmp_wkt,'dataload');
	}



	if ($('#gtu_georeference_ref').val() != ''){
		
		var tmp_ref=$('#gtu_georeference_ref').val();
        init_georef(tmp_ref);
	}
	
	
	
	$('.clean_map').click(
		function()
		{
			console.log("CLEAN");
			$('.georeference_ref').val("");
			$('.wfs_json').val("");
			$('.wfs_seach').val("");
			$('.wkt_search').val("");
			$('#chosen_layer').val("");
			removeDarwinLayer(iLayer);
		}
	);

});

function addSubGroup(selected_group, default_type, value)
{
    hideForRefresh('#gtu_group_screen');
    fieldset = $('fieldset[alt="'+selected_group+'"]');
    if( fieldset.length ==0 )
    {
      addGroup(selected_group, default_type, value);
    }
    list =  fieldset.find('>ul');
    $.ajax({
      type: "GET",
      url: $('.tag_parts_screen').attr('alt')+'/group/'+ selected_group + '/num/' + (0+$('.tag_parts_screen ul li').length),
      success: function(html)
      {
        html = $(html);
        html.find('.complete_widget select').val(default_type);

        if(value != undefined && value !='')
        {
          html.find('.tag_encod input').val(value);
        }
        list.append(html);

        showAfterRefresh('#gtu_group_screen');
      }
    });
}

function addTagToGroup(group, sub_group, tag)
{

  if($('fieldset[alt="'+group+'"] .complete_widget input, fieldset[alt="'+group+'"] .complete_widget option:selected').filter(function()
    { return $(this).is(':visible') && $(this).val() == sub_group; }).length == 0)
  {
    addSubGroup(group, sub_group, tag);
  }
  else
  {
    el = $('fieldset[alt="'+group+'"] .complete_widget input, fieldset[alt="'+group+'"] .complete_widget option:selected').filter('[value="'+sub_group+'"]');
    el_input = el.closest('li').find('.tag_encod input');
    el_input.val( el_input.val()  +' ; ' + tag);
  }
}

function disableUsedGroups()
{
  $('#groups_select option').removeAttr('disabled');
  $('.tag_parts_screen fieldset:visible').each(function()
  {
    var cur_group = $(this).attr('alt');
    $("#groups_select option[value='"+cur_group+"']").attr('disabled','disabled');
    if($("#groups_select option[value='"+cur_group+"']:selected"))
      $('#groups_select').val("");
  });
}

function addGroup(g_val, sub_group, value)
{

  if(g_val != '')
  {
    hideForRefresh('#gtu_group_screen');
    g_name = $('[value="'+g_val+'"]').text();
    $.ajax({
      type: "GET",
      url: $('.tag_parts_screen').attr('alt')+'/group/'+ g_val + '/num/' + (0+$('.tag_parts_screen ul li').length),
      success: function(html)
      {
        html = $(html);
        if( $('fieldset[alt="'+g_val+'"]').length == 0)
        {
          fld = '<fieldset alt="'+ g_val +'"><legend>' + g_name + '</legend><ul></ul><a class="sub_group"><?php echo __('Add Sub Group');?></a></fieldset>';
          $('.tag_parts_screen').append(fld);    
        }
        html.find('select').val(sub_group);
        fld_set = $('fieldset[alt="'+g_val+'"]');

        if(value != undefined && value !='')
        {
           html.find(' .tag_encod input').val(value);
        }

        fld_set.find('> ul').append(html);
        fld_set.show();

        disableUsedGroups();
        showAfterRefresh('#gtu_group_screen');
      }
    });
  }
}


//add predfined tag groups
//ftheeten 2018 08 08

//THIS part to prefil tags
<?php if($form->getObject()->isNew()&&strpos( $_SERVER['REQUEST_URI'],"new")&&strpos( $_SERVER['REQUEST_URI'],"duplicate_id")===FALSE): ?>		

          //ftheeten 2018 08 08
     var admLoaded=false;     
     var countryLoaded=false; 
     var provinceLoadedAdm=false; 
     var provinceLoaded=false; 
       
     var hydrographicLoaded=false;
     var seaLoadedAdm=false; 
     var seaLoaded=false;

     var populatedLoaded=false;
     var populatedPlaceLoadedAdm=false; 
     var populatedPlaceLoaded=false;

     var tagGroupFillListener=function()
     {
         if($('#gtu_newVal_0_sub_group_name').length)
       {
           if(!countryLoaded)
           {
               
                $('#gtu_newVal_0_sub_group_name').val('country');
                countryLoaded=true;
           }
           if(!provinceLoaded)
           {
                if(!provinceLoadedAdm)
                {
                    provinceLoadedAdm=true;
                    addGroup("administrative area");
                }
                if($('#gtu_newVal_1_sub_group_name').length)
                {                    
                    $('#gtu_newVal_1_sub_group_name').val('province');
                    provinceLoaded=true;
                    //launch hydrographic after all administrative displayed, otherwise HTML confused in HTML ids    
                   if(!hydrographicLoaded)
                   {                 
                        addGroup("hydrographic");
                        hydrographicLoaded=true;
                   }
                }
                
               
           }
           
           if(!seaLoaded)
           {
                if(!seaLoadedAdm)
                {
                    seaLoadedAdm=true;                   
                }
                if($('#gtu_newVal_2_sub_group_name').length)
                {                    
                    $('#gtu_newVal_2_sub_group_name').val('sea');
                    seaLoaded=true;
                    //launch populated after all hydrographic displayed, otherwise HTML confused in HTML ids    
                   if(!populatedLoaded)
                   {                 
                        addGroup("populated");
                        populatedLoaded=true;
                   }
                }
                
           }
           
           if(!populatedPlaceLoaded)
           {
                if(!populatedPlaceLoadedAdm)
                {
                    populatedPlaceLoadedAdm=true;                   
                }
                if($('#gtu_newVal_3_sub_group_name').length)
                {                    
                    $('#gtu_newVal_3_sub_group_name').val('populated place');
                    populatedPlaceLoaded=true;
                     //next tag group to be prefilled HERE
                }
                
           }
           
           
       }
     }    
     
     $(document).ajaxComplete(function(){
        tagGroupFillListener();
    }); 
    
    var addPredefinedTagGroups=function()
    {
       if(!admLoaded)
       {
      
        addGroup("administrative area");
        admLoaded=true;
       }
       
       
    }
    
    addPredefinedTagGroups();
<?php endif;?>    

/////THIS part for DMS

//ftheeten 2016 09 05
function checkCoordSourceState()
{

    var selected=$( ".coordinates_source" ).val();
		
		var showDMS='display: table-cell';
		var showDD='display: None';
		var showUTM='display: None';
		/*var showDMS='display: table-cell';
		var showDD='display: table-cell';
		var showUTM='display: table-cell';*/
		if(selected=="DD")
		{
            
			showDMS='display: None';
			showDD='display: table-cell';
			showUTM='display: None';
			
		}
		else if(selected=="DMS")
		{
                   
			showDMS='display: table-cell';
			showDD='display: None';
			showUTM='display: None';
		}
		else if(selected=="UTM")
		{
                   
			showDMS='display: None';
			showDD='display: None';
			showUTM='display: table-cell';
			
		}
		$('.GroupDMS').attr('style',showDMS );
		$('.GroupDD').attr('style', showDD);
		$('.GroupUTM').attr('style', showUTM);
		
}

//ftheeten 2015 06 02
$(".coordinates_source").change(

	function()
	{
		
		checkCoordSourceState();
		//$('.butShowDMS option[value='+selected+']').attr('selected','selected');
	}

);



function convertCoordinatesDMS2DD()
{

	//if(coordViewMode==false)
	//{
   
		var latD=0.0;
			var latM=0.0;
			var latS=0.0;
			var latSign=1;
			if($(".DMSLatDeg").val().length > 0)
			{
				latD=$(".DMSLatDeg").val().replace(/\,/, ".");
			}
			if($(".DMSLatMin").val().length > 0)
			{
				latM=$(".DMSLatMin").val().replace(/\,/, ".");
			}
			if($(".DMSLatSec").val().length > 0)
			{
				latS=$(".DMSLatSec").val().replace(/\,/, ".");
			}
			if($(".DMSLatSign").val().length > 0)
			{
				latSign=$(".DMSLatSign").val();
			}
			var latDeci= latSign *(parseFloat(latD) + ( parseFloat(latM)/60) + ( parseFloat(latS)/3600));
			if($.isNumeric(latDeci)==false)
			{
				alert('values for DMS coordinates doesn\'t seem numeric, please check your input');
			}
			$(".convertDMS2DDLat").val(latDeci);
			
			
			var longD=0.0;
			var longM=0.0;
			var longS=0.0;
			var longSign=1;
			if($(".DMSLongDeg").val().length > 0)
			{
				longD=$(".DMSLongDeg").val().replace(/\,/, ".");
			}
			if($(".DMSLongMin").val().length > 0)
			{
				longM=$(".DMSLongMin").val().replace(/\,/, ".");
			}
			if($(".DMSLongSec").val().length > 0)
			{
				longS=$(".DMSLongSec").val().replace(/\,/, ".");
			}
			if($(".DMSLongSign").val().length > 0)
			{
				longSign=$(".DMSLongSign").val();
			}
			var longDeci= longSign *(parseFloat(longD) + ( parseFloat(longM)/60) + ( parseFloat(longS)/3600));
			if($.isNumeric(longDeci)==false)
			{
				alert('values for DMS coordinates doesn\'t seem numeric, please check your input');
			}
			else
			{
				$(".convertDMS2DDLong").val(longDeci);
			}
            update_point_on_map($(".convertDMS2DDLat").val(),$(".convertDMS2DDLong").val(), null);
		//}
}

function convertCoordinatesDD2DMS()
{
	//if(coordViewMode==false)
	//{
		//ftheeten 2015 05 25
	  //longitude
	  var lat=$(".convertDMS2DDLat").val();
	  var lng=$(".convertDMS2DDLong").val();
	  $(".DMSLongDeg").val(Math.floor(Math.abs(lng)));
	  $(".DMSLongSign option").filter(function()
			{
				if(lng<0.0)
				{
					return $(this).val()<0;
				}
				else
				{
					return $(this).val()>0;
				}
			}).attr('selected',true);
	  var decimalLongitude=Math.abs(lng)-Math.floor(Math.abs(lng));
	  decimalLongitudeResultMinute=Math.floor(decimalLongitude*60);
	   $(".DMSLongMin").val(decimalLongitudeResultMinute);
	  decimalsLongitudeForSeconds=Math.abs(lng)-Math.floor(Math.abs(lng))-(decimalLongitudeResultMinute/60);
	  $(".DMSLongSec").val(decimalsLongitudeForSeconds*3600);
	  
	  //latitude
		$(".DMSLatDeg").val(Math.floor(Math.abs(lat)));
	  $(".DMSLatSign option").filter(function()
			{
				if(lat<0.0)
				{
					return $(this).val()<0;
				}
				else
				{
					return $(this).val()>0;
				}
			}).attr('selected',true);
	  var decimalLatitude=Math.abs(lat)-Math.floor(Math.abs(lat));
	  decimalLatitudeResultMinute=Math.floor(decimalLatitude*60);
	   $(".DMSLatMin").val(decimalLatitudeResultMinute);
	  decimalsLatitudeForSeconds=Math.abs(lat)-Math.floor(Math.abs(lat))-(decimalLatitudeResultMinute/60);
	  $(".DMSLatSec").val(decimalsLatitudeForSeconds*3600);
        update_point_on_map($(".convertDMS2DDLat").val(),$(".convertDMS2DDLong").val(), null);
    //}
}

$(".convertDMS2DDGeneralOnLeave").mouseleave(
	function(event)
	{
		var idControl=event.target.id;
		var value=$("#"+idControl).val();
		if(value.trim().length>0)
		{

				convertCoordinatesDMS2DD();
				//changeCoordinateSource(0);
			
		}
	}
);

$(".convertDMS2DDGeneralOnLeave").change(
	function(event)
	{
		coordViewMode=false;
		//changeCoordinateSource(0);

	}
);


$(".convertDD2DMSGeneral").change(
	function(event)
	{

		coordViewMode=false;

		convertCoordinatesDD2DMS();
		//changeCoordinateSource(1);

	}
);


$(".convertDMS2DDGeneralOnChange").change(
	function(event)
	{
		coordViewMode=false;

		convertCoordinatesDMS2DD();
		//changeCoordinateSource(0);
		
	}
);


$(".convertDD2DMSGeneral").mouseleave(
	function(event)
	{
		//alert("DD leave");
		var idControl=event.target.id;
		var value=$("#"+idControl).val();
		if(value.trim().length>0)
		{

				convertCoordinatesDD2DMS();
				//changeCoordinateSource(1);
			
		}
	}
);

//ftheeeten 20150610
//to prevent accidental updates of coordibates on mouseleave (as the  GTU are always displayed in "edit" mode)
function detectBothValCoordExisting()
{
	var booleanAlreadyExisting=false;
	var latDD=$(".convertDMS2DDLat").val().trim();
	var latDMS=$(".DMSLatDeg").val().trim();
	var longDD=$(".convertDMS2DDLong").val().trim();
	var longDMS=$(".DMSLongDeg").val().trim();
	if((latDD.length>0||latDMS.length>0)&&(longDD.length>0||longDMS.length>0))
	{
		booleanAlreadyExisting=true;
	}
	return booleanAlreadyExisting;
}


//ftheeten 2016 02 05
//UTM
$(".UTM2DDGeneralOnLeave").change(
	function(event)
	{

		convertUTM();

	}
);

function convertUTM()
{
		zone=$(".UTMZone").val();
		var zoneUTM =  initUTM('tmp', zone.replace( /\D+/g, ''), zone.replace( /[0-9]*/g, ''));

		var wgs84=proj4('EPSG:4326');
		var lat=$(".UTMLat").val();
	    var lng=$(".UTMLong").val();
		var conv=proj4(zoneUTM,wgs84,[lng,lat]);

		$(".convertDMS2DDLat").val(conv[1].toFixed(4));
		$(".convertDMS2DDLong").val(conv[0].toFixed(4));
	update_point_on_map($(".convertDMS2DDLat").val(),$(".convertDMS2DDLong").val(), null);
		
}

function initUTM(name, zone, direction )
{
	
	var dir="";
	if(direction=="S")
	{
		dir="+ south";
	}
	var strProj='+proj=utm +zone='+zone+' '+dir+' +datum=WGS84 +units=m +no_defs ';

	return strProj;
}

        //rmca 2016 06 21--
        $(".take_specimen_code").click(
		function()
		{
        
            var code_word="";
            if(window.opener.$("#specimen_newCodes_0_code_prefix").length>0)
            {
                
                code_word="#specimen_newCodes_0_code";
            }
            else if(window.opener.$("#specimen_Codes_0_code_prefix").length>0)
            {
                    code_word="#specimen_Codes_0_code";
            }
			
			var valSpecCodePrefix= window.opener.$(code_word+"_prefix").val()||'';
			var valSpecCodePrefixSeparator= window.opener.$(code_word+"_prefix_separator").val()||'';
			var valSpecCode= window.opener.$(code_word).val()||'';
			var valSpecCodeSuffixSeparator= window.opener.$(code_word+"_suffix_separator").val()||'';
			var valSpecCodeSuffix= window.opener.$(code_word+"_suffix").val()||'';
			var codeTotal=valSpecCodePrefix.concat(valSpecCodePrefixSeparator.concat(valSpecCode.concat(valSpecCodeSuffixSeparator.concat(valSpecCodeSuffix))));
			 if(!!codeTotal)
            {
                    $("#gtu_code").val(codeTotal);
            }
            else
            {
                 $("#gtu_code").val('');
            }
		});
        
         //ftheeten 2016 06 28
        $(".take_gtu_code").click( function()
        {
           var idGTU=window.opener.$(".view_loc_code").text();
           
            if(!!idGTU)
            {
                    $("#gtu_code").val(idGTU);
            }
            else
            { 
                 $("#gtu_code").val('');
            }
        });
        
         //ftheeten 2016 06 28
        $(".take_ig_code").click( function()
        {
           var idGTU=window.opener.$("#specimen_ig_ref_name").val();
            if(!!idGTU)
            {
                    $("#gtu_code").val(idGTU);
            }
            else
            {
                 $("#gtu_code").val('');
            }
        });
		
		$('#gtu_georeference_ref').on("change",
		function()
		{
		    console.log("change");
			var tmp_ref=$('#gtu_georeference_ref').val();
			init_georef(tmp_ref);
			
		}
	);
        

		
		

	
</script>
