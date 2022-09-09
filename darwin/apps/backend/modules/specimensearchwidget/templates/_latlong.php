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
		<td>Coordinate format</td>	
		<td colspan="2">
			<select id="coord_format" name="coord_format">
				<option value="dd">Decimal degrees (EPSG:4326)</option>
				<option value="dms">Degrees Minutes seconds (EPSG:4326) </option>
			</select>
		</td>
	</tr>
    <tr>
      <td>
      </td>
      <th>
        <?php echo __("Between");?>
      </th>
      <th>
        <?php echo __("And");?>
      </th>
    </tr>
    <tr class="coord_dd">
      <th class="right_aligned"><?php echo $form['lat_from']->renderLabel();?></th>
      <td><?php echo $form['lat_from'];?></td>
      <td><?php echo $form['lat_to'];?><?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?></td>
    </tr>
	<!--DMS-->
	<tr class="coord_dms" >	
		<td>Lat (deg)</td>
		<td><input type="text" class="coord_dms" id="lat_deg_from" name="lat_deg_from"></td>
		<td><input type="text" class="coord_dms" id="lat_deg_to" name="lat_deg_to"></td>
	</tr>
	<tr class="coord_dms">	
		<td>Lat (min)</td>
		<td><input type="text" class="coord_dms" id="lat_min_from" name="lat_min_from"></td>
		<td><input type="text"  class="coord_dms" id="lat_min_to" name="lat_min_to"></td>
	</tr>
	<tr class="coord_dms">	
		<td>Lat (sec)</td>
		<td><input type="text" class="coord_dms" id="lat_sec_from" name="lat_sec_from"></td>
		<td><input type="text" class="coord_dms" id="lat_sec_to" name="lat_sec_to"></td>
	</tr>
	<tr class="coord_dms">
		<td>Lat (dir)</td>
		<td><select class="coord_dms" id="lat_dir_from" name="lat_dir_from" >
			<option value="1">N</option>
			<option value="-1">S</option>
		</select></td>
		<td><select class="coord_dms" id="lat_dir_to" name="lat_dir_to" >
			<option value="1">N</option>
			<option value="-1">S</option>
		</select></td>
	</tr>
	<!--DMS-->
    <tr class="coord_dd">
      <th class="right_aligned"><?php echo  $form['lon_from']->renderLabel();?></th>
      <td><?php echo $form['lon_from'];?></td>
      <td><?php echo $form['lon_to'];?><?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?></td>
    </tr>
	
	<!--DMS-->
	<tr class="coord_dms" >	
		<td>Long (deg)</td>
		<td><input type="text" class="coord_dms" id="long_deg_from" name="long_deg_from"></td>
		<td><input type="text" class="coord_dms" id="long_deg_to" name="long_deg_to"></td>
	</tr>
	<tr class="coord_dms">	
		<td>Long (min)</td>
		<td><input type="text" class="coord_dms" id="long_min_from" name="long_min_from"></td>
		<td><input type="text" class="coord_dms" id="long_min_to" name="long_min_to"></td>
	</tr>
	<tr class="coord_dms">	
		<td>Long (sec)</td>
		<td><input type="text" class="coord_dms" id="long_sec_from" name="long_sec_from"></td>
		<td><input type="text" class="coord_dms" id="long_sec_to" name="long_sec_to"></td>
	</tr>
	<tr class="coord_dms">
		<td>Long (dir)</td>
		<td><select class="coord_dms" id="long_dir_from" name="long_dir_from" >
			<option value="-1">W</option>
			<option value="1">E</option>
		</select></td>
		<td><select class="coord_dms" id="long_dir_to" name="long_dir_to" >
			<option value="-1">W</option>
			<option value="1">E</option>
		</select></td>
	</tr>
	<!--DMS-->
  </table>
</div>
  <div id="map_search_form" class="hidden">
    <div>
        <div style="width: 600px; height:400px" id="smap">
                   <div class="ol-control draw_rectangle">
                   <input  type="button"  value="T"></button>
                   </div>
        </div>
        <div id="mouse-position"></div>    
    </div>        
        <select id="layer-select">
                       <option value="Aerial">Aerial</option>
                       <option value="AerialWithLabels" selected>Aerial with labels</option>
                       <option value="Road">Road (static)</option>
                       <option value="RoadOnDemand">Road (dynamic)</option>
        </select>
        <table>        
        <tr>
            <td><?php echo $form['wkt_search']->renderLabel();?></td><td><?php echo $form['wkt_search']->render();?></td>
        </tr>
        </table>
  </div>
<div>
<!--<?php print(__("Include locality tags ?"));?><?php echo $form['include_text_place'];?>-->
<div>

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
				key: " <?php print(sfConfig::get('dw_bing_key'));?>",
				imagerySet: styles[i]
				// use maxZoom 19 to see stretched tiles instead of the BingMaps
				// "no photos at this zoom level" tiles
				// maxZoom: 19
			  })
			}));
		}
	  
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
        mousePositionControl.setProjection("EPSG:4326");
        
        map.on('dblclick', function(evt) {            
            if(type_draw=="box")            {
              
                draw.finishDrawing();
            }
        });  
                
        //select background
      var select = document.getElementById('layer-select');
		function onChange() {
			var style = select.value;
			for (var i = 0, ii = layers.length; i < ii; ++i) {
			  layers[i].setVisible(styles[i] === style);
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
				  
				    $(".coord_dms").val("");	
                   
                  
                  $('#lat_long_set table').hide(); 
                  $('#map_search_form').show();
                   map.updateSize();
                } else {                  
                  $('#lat_long_set table').show();
                  $('#map_search_form').hide();
                }
              });

       
		
		//coord dms to dd
		var test_coordinate_format=function()
		{
			if($("#coord_format").val()=="dd")
			{
				$(".coord_dd").show();
				$(".coord_dms").hide();
			}
			else if($("#coord_format").val()=="dms")
			{
				$(".coord_dd").hide();
				$(".coord_dms").show();
			}
		}
		
		$("#coord_format").change(function()
			{
				test_coordinate_format();
			}
		)
		
		$(".coord_dd").change(
			function()
			{			
				if($("#specimen_search_filters_lat_from").val()!=="" &&$("#specimen_search_filters_lat_to").val()!=="")
				{
					var high=$("#specimen_search_filters_lat_to").val();
					var low=$("#specimen_search_filters_lat_from").val();
					if(parseFloat(high)<parseFloat(low))
					{						
						$("#specimen_search_filters_lat_to").val(low);
						$("#specimen_search_filters_lat_from").val(high);
					}
				}
				if($("#specimen_search_filters_lon_from").val()!=="" &&$("#specimen_search_filters_lon_to").val()!=="")
				{
					var high=$("#specimen_search_filters_lon_to").val();
					var low=$("#specimen_search_filters_lon_from").val();
					if(parseFloat(high)<parseFloat(low))
					{						
						$("#specimen_search_filters_lon_to").val(low);
						$("#specimen_search_filters_lon_from").val(high);
					}
				}
			}
		);
		
		var convert_to_dms=function(deg, min, sec, direction, target_ctrl)
		{			
			if($.isNumeric(deg))
			{
				if(min=="")
				{
					min=0;
				}
				if(sec=="")
				{
					sec=0;
				}
			}
			if($.isNumeric(deg)&&$.isNumeric(min)&&$.isNumeric(sec)&&$.isNumeric(direction))
			{
				console.log("convert2")
				var dd=parseInt(deg);
				dd=dd+(parseFloat(min)/60);
				dd=dd+(parseFloat(sec)/3600);
				dd=dd*parseInt(direction);				
				$(target_ctrl).val(dd);

			}
		}
		$(".coord_dms").change(
			function()
			{
				
				var deg_lat_from=$("#lat_deg_from").val();
				var min_lat_from=$("#lat_min_from").val();
				var sec_lat_from=$("#lat_sec_from").val();
				var sec_dir_from=$("#lat_dir_from").val();
				convert_to_dms(deg_lat_from, min_lat_from , sec_lat_from, sec_dir_from, "#specimen_search_filters_lat_from");
				
				
				var deg_lat_to=$("#lat_deg_to").val();
				var min_lat_to=$("#lat_min_to").val();
				var sec_lat_to=$("#lat_sec_to").val();
				var sec_dir_to=$("#lat_dir_to").val();
				convert_to_dms(deg_lat_to, min_lat_to , sec_lat_to, sec_dir_to, "#specimen_search_filters_lat_to");
				
				var deg_long_from=$("#long_deg_from").val();
				var min_long_from=$("#long_min_from").val();
				var sec_long_from=$("#long_sec_from").val();
				var sec_dir_from=$("#long_dir_from").val();
				convert_to_dms(deg_long_from, min_long_from , sec_long_from, sec_dir_from, "#specimen_search_filters_lon_from");
				
				
				var deg_long_to=$("#long_deg_to").val();
				var min_long_to=$("#long_min_to").val();
				var sec_long_to=$("#long_sec_to").val();
				var sec_dir_to=$("#long_dir_to").val();
				convert_to_dms(deg_long_to, min_long_to , sec_long_to, sec_dir_to, "#specimen_search_filters_lon_to");
				
			}
		);
		test_coordinate_format();
	}
    );
init_map();
</script>
