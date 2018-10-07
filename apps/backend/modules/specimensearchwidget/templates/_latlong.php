<script language="JavaScript" type="text/javascript" src="<?php print(public_path('/openlayers/v5.2.0-dist/ol.js'));?>"></script>
<link rel="stylesheet" href="<?php print(public_path('/openlayers/v5.2.0-dist/ol.css'));?>">
<div id="lat_long_set">
 <!--ftheeten 2018 10 05-->
    <style>

      
      .draw-box {
        top: 65px;
        left: .5em;
        width: 1.375em;
        height: 1.375em; 
        background-color: rgba(255,255,255,.4); 
        text-align: center; 
           
      }
      
      .draw-polygon {
        top: 100px;
        left: .5em;
        width: 1.375em;
        height: 1.375em; 
        background-color: rgba(255,255,255,.4); 
        text-align: center; 
           
      }
      
      
      .move-map {
        top: 135px;
        left: .5em;
        width: 1.375em;
        height: 1.375em; 
        background-color: rgba(255,255,255,.4); 
        text-align: center; 
           
      }
     
     
      
    </style>
  <p><strong><?php echo __('Choose latitude/longitude on map');?></strong><input type="checkbox" id="show_as_map"></p>
  <br /><br />
  <table>
    <tr>
      <td>
      </td>
      <th>
        <?php echo $form['lat_from']->renderLabel();?>
      </th>
      <th>
        <?php echo $form['lon_from']->renderLabel();?>
      </th>
    </tr>
    <tr>
      <th class="right_aligned"><?php echo __('Between');?></th>
      <td><?php echo $form['lat_from'];?></td>
      <td><?php echo $form['lon_from'];?><?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?></td>
    </tr>
    <tr>
      <th class="right_aligned"><?php echo __('And');?></th>
      <td><?php echo $form['lat_to'];?></td>
      <td><?php echo $form['lon_to'];?><?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?></td>
    </tr>
  </table>
</div>
  <div id="map_search_form" class="hidden">
    <div>
        <div style="width: 600px; height:400px" id="smap">
                   
        </div>
        <div id="mouse-position"></div>    
    </div>        
        <select id="layer-select">
                       <option value="Aerial">Aerial</option>
                       <option value="AerialWithLabels" selected>Aerial with labels</option>
                       <option value="Road">Road (static)</option>
                       <option value="RoadOnDemand">Road (dynamic)</option>
					   <option value="OSM">OpenStreetMap</option>
        </select>
        <table>        
        <tr>
            <td><?php echo $form['wkt_search']->renderLabel();?></td><td><?php echo $form['wkt_search']->render();?></td>
        </tr>
        </table>
  </div>



<script  type="text/javascript">
    var results;
   // initSearchMap();
    
    //ftheeten 2018 10 05
    var mousePositionControl;
	var scaleLineControl;
	var map;
    var styleLine;
    var source_draw = new ol.source.Vector({wrapX: false});
    var draw;
    var iLayer=0;
    var vectorLoaded =false;
    var type_draw="";
	var OSM_layer;
   
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
            
    	function addDarwinLayer(feature,origininput)
        {
            var tmp_geom =new ol.geom.Polygon(feature.getGeometry().getCoordinates());
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
        }
        
	function init_map(){
    
		mousePositionControl= new ol.control.MousePosition({
			 coordinateFormat: ol.coordinate.createStringXY(4),
			projection:'EPSPG:4326',
			className: "custom-mouse-position",
			target: document.getElementById("mouse-position"),
			undefinedHTML: "&nbsp;"
		});
		scaleLineControl = new ol.control.ScaleLine();
			
		

		styleLine= new ol.style.Style({
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
		})
		
		source = new ol.source.Vector();
		vectorlayer = new ol.layer.Vector({
			source: source,
			style:styleLine
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
	  
		layers [styles.length] = vectorlayer;
        
     
     //button draw bbox
      DrawBoxControl = function(opt_options) {
                
                
                var options = opt_options || {};
                var element = document.createElement('div');
                element.className = 'draw-box ol-unselectable ol-control';
                element.innerHTML='&#9633;';   
                $(element).click(
                    function()
                    {
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
                            addDarwinLayer(event.feature,"from drawing");
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
                         type_draw="polygon";
                        removeDarwinLayer(iLayer);
                        map.removeInteraction(draw);
                        draw = new ol.interaction.Draw({
                        source: source_draw,
                        type: 'Polygon'
                        });
                        draw.on('drawend', function (event) {
                            addDarwinLayer(event.feature,"from drawing");
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
     
       		map = new ol.Map({
				target: 'smap',
				layers: layers,    
				 
				view: new ol.View({                    
				  center: ol.proj.fromLonLat([0,0]),
				  zoom: 2
				}),
				controls: ol.control.defaults({
						attributionOptions: ({collapsible: false})
				}).extend([mousePositionControl, scaleLineControl,  new DrawBoxControl(), new DrawPolygonControl(), new MoveMapControl()])
		});
		 map.addLayer(OSM_layer);
        mousePositionControl.setProjection("EPSG:4326");
        
        map.on('dblclick', function(evt) {            
            if(type_draw=="box")            {
              
                draw.finishDrawing();
            }
        });  
                
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
        

		
	}
    
   // drawmap();
    
    $(document).ready(
        function()
        {
              $('#show_as_map').click(function(){
                if($(this).is(':checked')) {             
                  $('#specimen_search_filters_lat_from').val("");
                  $('#specimen_search_filters_lon_from').val("");
                  $('#specimen_search_filters_lat_to').val("");
                  $('#specimen_search_filters_lon_to').val("");
                   
                  
                  $('#lat_long_set table').hide(); 
                  $('#map_search_form').show();
                   map.updateSize();
                } else {                  
                  $('#lat_long_set table').show();
                  $('#map_search_form').hide();
                }
              });

        }
    );
init_map();
</script>

