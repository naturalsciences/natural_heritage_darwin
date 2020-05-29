<script language="JavaScript" type="text/javascript" src="<?php print(public_path('/openlayers/v5.2.0-dist/ol.js'));?>"></script>
<link rel="stylesheet" href="<?php print(public_path('/openlayers/v5.2.0-dist/ol.css'));?>"> 
 <div class="container">
    <table id="gtu_search">
      <thead>
        <tr>
          <th colspan="4"><?php echo $form['gtu_code']->renderLabel() ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="4"><?php echo $form['gtu_code']->render() ?></td>
        </tr>
        <tr>
          <th><?php echo $form['gtu_from_date']->renderLabel(); ?></th>
          <th><div class="to_date_group"><?php echo $form['gtu_to_date']->renderLabel(); ?></div></th>
          <th colspan="2"><?php echo __("Precise date"); ?></th>
         
        </tr>
        <tr>
          <td><?php echo $form['gtu_from_date']->render() ?></td>
          <td><div class="to_date_group"><?php echo $form['gtu_to_date']->render() ?><div></td>
          <td colspan="2"><?php echo $form['gtu_from_precise']->render() ?></td>
          
        </tr>
        <tr>
          <th colspan="3"><?php echo $form['tags']->renderLabel() ?><?php print(__(" (use * to find part of words. <br/>EG. : - '*mer*' will match 'Erpe-Mere', 'Merksplas', 'Mer du Nord', etc...<br/> - 'mer' will match the word 'mer' like in 'Mer du Nord', 'Mer Egée', 'Mer Méditerranée' etc...)"))?></th>
          <th colspan="1"></th>
        </tr>
        <?php foreach($form['Tags'] as $i=>$form_value):?>
          <?php include_partial('specimensearch/andSearch',array('form' => $form['Tags'][$i], 'row_line'=>$i));?>
        <?php endforeach;?>
        <tr class="and_row">
          <td colspan="3"></td>
          <td><a href="<?php echo url_for('specimensearch/andSearch');?>" class="and_tag"><?php echo image_tag('add_blue.png');?></a><?php echo $form['tag_boolean']->render(); ?></td>
        </tr>
      </tbody>
    </table>
    <script  type="text/javascript">
      var num_fld = 1;
      $('.and_tag').click(function()
      {
        hideForRefresh('#refGtu');
        $.ajax({
          type: "GET",
          url: $(this).attr('href') + '/num/' + (num_fld++) ,
          success: function(html)
          {
            $('table#gtu_search > tbody .and_row').before(html);
            showAfterRefresh('#refGtu');
          }
        });
        return false;
      });    

    $(document).ready(
        function()
        {        
            $(".precise_gtu_date").click(
                function(e)
                {
                   
                   if($(".precise_gtu_date").is(':checked'))
                   {               
                        $("[id^=specimen_search_filters_gtu_from_date]").each( 
                            function(  )
                            {
                                 $(".to_date_group").hide();
                                align($("#"+ this.id));
                            }
                        );
                   }
                   else
                   {
                       $(".to_date_group").show();
                       $("[id^=specimen_search_filters_gtu_to_date]").val($("[id^=specimen_search_filters_gtu_from_date] option:first").val());
                   }
                }
            );
            
            function align(ctrl)
            {
                 if($(".precise_gtu_date").is(':checked'))
                   {                 
                        var name_ctrl=ctrl.attr("id");
                        var val_ctrl = ctrl.val();                    
                        name_ctrl=name_ctrl.replace(/\_from_date\_/g, "_to_date_");                   
                        $("#"+name_ctrl+ " option[value=" + val_ctrl +"]").attr('selected','selected');
                    }
            }
            
            $("[id^=specimen_search_filters_gtu_from_date]").change(
                function()
                {  
                    align($(this));
                }
            );
        }
        );      
    </script>
  </div>
  
<div id="lat_long_set" >
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
  <p><strong><?php echo __('Choose latitude/longitude on map');?></strong><input type="checkbox" id="show_as_map" checked></p>
  <br /><br />
  <table class="hidden">
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
  <div id="map_search_form">
    <div >
        <div style="width: 100%; height:500px; display:inline-block" id="smap">
         <select id="addwms" class="form-control">
				<?php foreach(sfConfig::get('dw_wfs_layers') as $url=>$name):?>
					<option value="<?php print($url);?>"><?php print($name);?></option>
				<?php endforeach;?>				  
			</select>
        <input id="browse_wms" type="button" value="Browse layers"></input>		
		<select id="addwmslayer" class="form-control">
			
        </select>
		<input id="put_layer" type="button" value="Add layers"></input>
		<select id="layer-select">
                       <option value="Aerial">Aerial</option>
                       <option value="AerialWithLabels" selected>Aerial with labels</option>
                       <option value="Road">Road (static)</option>
                       <option value="RoadOnDemand">Road (dynamic)</option>
					   <option value="OSM">OpenStreetMap</option>
        </select>
		<br/>
		Selected layers :
		<input type="text" id="chosen_layer" style="width:70%" readonly>
		 <input id="remove_last" type="button" value="Remove last"></input>	
        </div>
        <div id="mouse-position"></div>    
    </div>   
		<div id="wms_list"></div>
        
        <table> 			
        <tr>
            <td><?php echo $form['wkt_search']->renderLabel();?></td><td><?php echo $form['wkt_search']->render();?></td>
        </tr>
		<tr>
            <td><?php echo $form['wfs_search']->renderLabel();?></td><td><?php echo $form['wfs_search']->render();?></td>
        </tr>
        </table>
  </div>
<div>
<?php print(__("Include locality tags ?"));?><?php echo $form['include_text_place'];?>
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
	var OSM_layer;
	var full_screen=false;
	var globalLayers=Array();
	var current_wms;
	var current_layer_name;
	//var WKTArray=Array();
	var WFSArray=Array();
	var LayerArray=Array();
	var wfs_url="<?php print(sfConfig::get('dw_root_url_wfs'));?>";
	
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
	
	var remove_last=function()
	{
		WFSArray.pop();
		LayerArray.pop();
		createMultiPolygon();
		$('#chosen_layer').val(LayerArray.join("; "));
	}
	
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
	
	var addLayer=function(wms_point, layer_name)
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
	
	$("#browse_wms").click(
		function()
		{
			var wms_url=$("#addwms").val();					
            parseCapabilities(wms_url);
			current_wms=wms_url;
		}
	);
	
	$("#remove_last").click(
		function()
		{
			remove_last();
		}
	);
	
   $("#put_layer").click(
		function()
		{
			var wms_url=$("#addwmslayer").val();					
            addLayer($("#addwms").val(),$("#addwmslayer").val());
		}
	);
   
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
	 
	
	  
	  var fullScreenControl = new ol.control.FullScreen();
	  
     //ol.inherits(FullScreenControl, FullScreen );
     
     
       		map = new ol.Map({
				target: 'smap',
				layers: layers,    
				 
				view: new ol.View({                    
				  center: ol.proj.fromLonLat([0,0]),
				  zoom: 2
				}),
				controls: ol.control.defaults({
						attributionOptions: ({collapsible: false})
				}).extend([mousePositionControl, scaleLineControl,  new DrawBoxControl(), new DrawPolygonControl(), new MoveMapControl(), fullScreenControl])
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
			//console.log(select.value)
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
				//console.log("trye");
				for (var i = 0, ii = layers.length; i < ii; ++i) {
				  layers[i].setVisible(false);
				}
				OSM_layer.setVisible(true);
			}
		}
		select.addEventListener('change', onChange);
		onChange();  

		map.on('singleclick', function(evt) {
			if(globalLayers.length>0)
			{
								
				var lonlat = map.getCoordinateFromPixel(evt.pixel);
				lonlat= ol.proj.transform(lonlat, "EPSG:3857", "EPSG:4326");
				//console.log(lonlat);
				var filter="INTERSECTS(geom, POINT ("+ lonlat[1] +" "+ lonlat[0] +"))";
				
				var query_url=wfs_url + current_wms+"/wfs?service=wfs&version=2.0.0&request=GetFeature&typeNames="+ current_layer_name +"&cql_filter="+filter;
				//console.log(query_url);
				$.get( query_url)
				  .done(function( data ) {
	
						
						$(data).find("gml\\:name").each(
							function(index, obj)
							{
								//console.log(obj);
								var name=obj.childNodes[0].nodeValue;
								//console.log(name);
								LayerArray.push(name);
								//var tmp={layer: current_layer_name ,'value':name}
								//WFSArray.push(tmp);
							}
						);
						$(data).find(current_wms +"\\:gid").each(
							function(index, obj)
							{
								//console.log(obj);
								var gid=obj.childNodes[0].nodeValue;
								
								
								var tmp={layer: current_layer_name ,'value':gid}
								WFSArray.push(tmp);
							}
						);
						createMultiPolygon();						
				  });
			}
	});		
        

		
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

