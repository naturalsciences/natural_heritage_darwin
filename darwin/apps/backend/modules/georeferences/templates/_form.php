<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<script language="JavaScript" type="text/javascript" src="<?php print(public_path('/openlayers/v5.2.0-dist/ol.js'));?>"></script>
<link rel="stylesheet" href="<?php print(public_path('/openlayers/v5.2.0-dist/ol.css'));?>">

<!--[if lte IE 8]>
    <link rel="stylesheet" href="/leaflet/leaflet.ie.css" />
<![endif]-->

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

<script type="text/javascript">
$(document).ready(function () 
{
  $('body').catalogue({});
});
</script>

<?php echo form_tag('georeferences/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()), array('class'=>'edition'));?>

<?php if (!$form->getObject()->isNew()): ?>
	<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
<table>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th class="top_aligned"><?php echo $form['data_origin']->renderLabel() ?></th>
        <td>
          <?php echo $form['data_origin']->renderError() ?>
          <?php echo $form['data_origin'] ?>
        </td>
      </tr>
	  <tr>
        <th class="top_aligned"><?php echo $form['tag_group_name']->renderLabel() ?></th>
        <td>
          <?php echo $form['tag_group_name']->renderError() ?>
          <?php echo $form['tag_group_name'] ?>
        </td>
      </tr>
	   <tr>
        <th class="top_aligned"><?php echo $form['tag_sub_group_name']->renderLabel() ?></th>
        <td>
          <?php echo $form['tag_sub_group_name']->renderError() ?>
          <?php echo $form['tag_sub_group_name'] ?>
        </td>
      </tr>
	   <tr>
        <th class="top_aligned"><?php echo $form['wfs_url']->renderLabel() ?></th>
        <td>
          <?php echo $form['wfs_url']->renderError() ?>
          <?php echo $form['wfs_url'] ?>
        </td>
      </tr>
	   <tr>
        <th class="top_aligned"><?php echo __("WFS layer");?></th>
        <td>
          <?php echo $form['wfs_table']->renderError() ?>
          <?php echo $form['wfs_table'] ?>
        </td>
      </tr>
	  <tr>
        <th class="top_aligned"><?php echo __("WFS ID"); ?></th>
        <td>
          <?php echo $form['wfs_id']->renderError() ?>
          <?php echo $form['wfs_id'] ?>
        </td>
      </tr>
	  <tr>
        <th class="top_aligned"><?php echo $form['name']->renderLabel() ; ?></th>
        <td>
          <?php echo $form['name']->renderError() ?>
          <?php echo $form['name'] ?>
        </td>
      </tr>
	  <tr>
        <th class="top_aligned"><?php echo $form['validation_level']->renderLabel() ; ?></th>
        <td>
          <?php echo $form['validation_level']->renderError() ?>
          <?php echo $form['validation_level'] ?>
        </td>
      </tr>
	   <tr>
        <th class="top_aligned"><?php echo $form['validation_comment']->renderLabel() ; ?></th>
        <td>
          <?php echo $form['validation_comment']->renderError() ?>
          <?php echo $form['validation_comment'] ?>
        </td>
      </tr>
	  <tr>
        <th class="top_aligned"><?php echo $form['query_date']->renderLabel() ; ?></th>
        <td>
          <?php echo $form['query_date']->renderError() ?>
          <?php echo $form['query_date'] ?>
        </td>
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
							
								center: ol.proj.fromLonLat([0,0]),
							
							  zoom: 7
							}),
							controls: ol.control.defaults({
									attributionOptions: ({collapsible: false})
							}).extend([mousePositionControl, scaleLineControl,new DrawPointControl(), new DrawBoxControl(), new DrawPolygonControl(), new DrawLineControl(), new MoveMapControl(), new DeleteControl()])
					});
					mousePositionControl.setProjection("EPSG:4326");
				   
				   map.addLayer(OSM_layer);
					
							
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
			<?php if(!$form->getObject()->isNew()):?>
			array_wkt.push("<?php print($form->getObject()->getWkt()); ?>");
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
					console.log("check");
					$("#select_wfs_item").prop("checked", true);
					$("#select_wfs_item").trigger("change");
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
    </tbody>
</table>
<!--JMHerpers 2019 05 29-->

<table>
    <tfoot>
      <tr>
        <td>
          <?php echo $form->renderHiddenFields(true) ?>

          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('New'), 'georeferences/new') ?>
            &nbsp;<?php echo link_to(__('Duplicate '), 'georeferences/new?duplicate_id='.$form->getObject()->getId()) ?>
          <?php endif?>

          &nbsp;<a href="<?php echo url_for('georeferences/index') ?>"><?php echo __('Cancel');?></a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to(__('Delete'), 'georeferences/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>



