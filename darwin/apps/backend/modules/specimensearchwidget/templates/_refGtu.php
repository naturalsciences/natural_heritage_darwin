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

      .qtip { max-width: none !important; }
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
        <?php echo $form['lat_from']->renderLabel();?>
      </th>
      <th>
        <?php echo $form['lon_from']->renderLabel();?>
      </th>
    </tr>
    <tr class="coord_dd">
      <th class="right_aligned"><?php echo __('Between');?></th>
      <td><?php echo $form['lat_from'];?></td>
      <td><?php echo $form['lon_from'];?><?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?></td>
    </tr>
	<!--DMS-->
	<tr class="coord_dms" >	
		<td>Min (deg)</td>
		<td><input type="text" class="coord_dms" id="lat_deg_from" name="lat_deg_from"></td>
		<td><input type="text" class="coord_dms" id="long_deg_from" name="long_deg_from"></td>
	</tr>
	<tr class="coord_dms">	
		<td>Min (min)</td>
		<td><input type="text" class="coord_dms" id="lat_min_from" name="lat_min_from"></td>
		<td><input type="text"  class="coord_dms" id="long_min_from" name="long_min_from"></td>
	</tr>
	<tr class="coord_dms">	
		<td>Min (sec)</td>
		<td><input type="text" class="coord_dms" id="lat_sec_from" name="lat_sec_from"></td>
		<td><input type="text" class="coord_dms" id="long_sec_from" name="long_sec_from"></td>
	</tr>
	<tr class="coord_dms">
		<td>Min (dir)</td>
		<td><select class="coord_dms" id="lat_dir_from" name="lat_dir_from" >
			<option value="1">N</option>
			<option value="-1">S</option>
		</select></td>
		<td><select class="coord_dms" id="long_dir_from" name="long_dir_from" >
			<option value="-1">W</option>
			<option value="1">E</option>
		</select></td>
	</tr>
	<!--DMS-->
    <tr class="coord_dd">
      <th class="right_aligned"><?php echo __('And');?></th>
      <td><?php echo $form['lat_to'];?></td>
      <td><?php echo $form['lon_to'];?><?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?></td>
    </tr>
	<!--DMS-->
	<tr class="coord_dms" >	
		<td>Max (deg)</td>
		<td><input type="text" class="coord_dms" id="lat_deg_to" name="lat_deg_to"></td>
		<td><input type="text" class="coord_dms" id="long_deg_to" name="long_deg_to"></td>
	</tr>
	<tr class="coord_dms">	
		<td>Max (min)</td>
		<td><input type="text" class="coord_dms" id="lat_min_to" name="lat_min_to"></td>
		<td><input type="text" class="coord_dms" id="long_min_to" name="long_min_to"></td>
	</tr>
	<tr class="coord_dms">	
		<td>Long (sec)</td>
		<td><input type="text" class="coord_dms" id="lat_sec_to" name="lat_sec_to"></td>
		<td><input type="text" class="coord_dms" id="long_sec_to" name="long_sec_to"></td>
	</tr>
	<tr class="coord_dms">
		<td>Max (dir)</td>
		<td><select class="coord_dms" id="lat_dir_to" name="lat_dir_to" >
			<option value="1">N</option>
			<option value="-1">S</option>
		</select></td>
		<td><select class="coord_dms" id="long_dir_to" name="long_dir_to" >
			<option value="-1">W</option>
			<option value="1">E</option>
		</select></td>
	</tr>
	<!--DMS-->
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
                       <!---<option value="Aerial">Aerial</option>
                       <option value="AerialWithLabels" selected>Aerial with labels</option>
                       <option value="Road">Road (static)</option>
                       <option value="RoadOnDemand">Road (dynamic)</option>-->
					   <option value="OSM">OpenStreetMap</option>
					   <option value="esri_satelite">ESRI Web service</option>
					   
        </select>
		<br/>
		Selected layers :
		<input type="text" id="chosen_layer" style="width:70%" readonly>
		 <input id="remove_last" type="button" value="Remove last"></input>
			<input type="button" value="<?php echo __("List from map");?>" name="btn_translate_wfs" id="btn_translate_wfs" class="result_choose"/>
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
		<tr>
            <td><?php echo $form['wfs_search_translated']->renderLabel();?></td><td><?php echo $form['wfs_search_translated']->render();?></td> 	
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
	
	var ol_ext_inherits = function(child,parent) {
		child.prototype = Object.create(parent.prototype);
		child.prototype.constructor = child;
	};
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
	  
		/*var styles = [
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
		}*/
		var layers = [];
		var styles=["esri_satelite"];
		var esri= new ol.layer.Tile({
		  source: new ol.source.XYZ({
			url:
			  'http://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
	
			  maxZoom:12
		  }),
		});
		layers.push(esri);
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
      ol_ext_inherits(DrawBoxControl, ol.control.Control);
     
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
     ol_ext_inherits(DrawPolygonControl, ol.control.Control);
     
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
     ol_ext_inherits(MoveMapControl, ol.control.Control);
	 
	
	  
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
			/*if(select.value!="OSM")
			{
				OSM_layer.setVisible(false);
				var style = select.value;
				for (var i = 0, ii = layers.length; i < ii; ++i) {
				  layers[i].setVisible(styles[i] === style);
				}
			}*/
			if(select.value=="esri_satelite")
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
				console.log(query_url);
				$.get( query_url)
				  .done(function( data ) {
	
						
						$(data).find("gml\\:name").each(
							function(index, obj)
							{
								//console.log(obj);
								var name=obj.childNodes[0].nodeValue;
								console.log(name);
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
								console.log(gid);
								
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
    $('#btn_translate_wfs').on('click',purposeTagsListWfs);
	
	 function purposeTagsTranslate_logic(name_session_item, value_item, url, p_data)
  {
	    sessionStorage.setItem(name_session_item, value_item);
	    var last_position = $("#gtu_search").offset().top ;
	    
		// $(".translate_modal").modal_screen();
		 $(this).qtip({
		  id: 'modal',
		  content: {
			text: '<img src="/images/loader.gif" alt="loading"> Loading ...',
			title: { button: true, text: "" },
			ajax: {
			     url: url,
					type: 'GET',
					// Take name in input if set
					data: p_data
			}
		  },
		  position: {
			my: 'top center',
			at: 'top center',
			adjust:{
			  y: 250 // option set in case of the qtip become too big
			},
			target: $(document.body)
		  },

		  show: {
			ready: true,
			delay: 0,
			event: event.type,
			solo: true,
			modal: {
			  on: true,
			  blur: false
			}
		  },
		  hide: {
			event: 'close_modal_gtu',
			target: $('body')
		  },
		  events: {
			show: function () {
			  ref_element_id = null;
			  ref_element_name = null;
			},
			hide: function(event, api) {
			  if(ref_element_id != null && ref_element_name != null)
			  {
				parent_el = api.elements.target.parent().prevAll('.ref_name');
				if(parent_el.get( 0 ).nodeName == 'INPUT')
				  parent_el.val(ref_element_name);
				else
				  parent_el.text(ref_element_name);
				parent_el.prev().val(ref_element_id);
				api.elements.target.parent().prevAll('.ref_clear').removeClass('hidden').show();
				api.elements.target.find('.off').removeClass('hidden');
				api.elements.target.find('.on').addClass('hidden');
				parent_el.prev().trigger('change');
				if (data_field_to_clean !== '') {
				  if ($('.'+data_field_to_clean).length) {
					$('.'+data_field_to_clean).val('');
				  }
				}
			  }
			  
			  scroll(0,last_position) ;
			  api.destroy();
			}
		  },
		  style: 'ui-tooltip-light ui-tooltip-rounded'
		},event);
		
		window.scrollTo(0, 250);
				
  }
  
   function purposeTagsListWfs(event)
  {
	  if($(".wfs_search").val().length>0)
	  {
		  var tmp_list=JSON.parse($(".wfs_search").val());
		  var i;
		  var layer;
		  var tmp_ids=Array();
		  
		  for(i=0;i<tmp_list.length; i++)
		  {
			  layer=tmp_list[i]["layer"];
			  tmp_ids.push(tmp_list[i]["value"]);
		  }
		  if(tmp_ids>0)
		  {
			
			var data= {with_js:1, layer: 'wfs.'+layer, ids:tmp_ids.join(",")};
			purposeTagsTranslate_logic("translated_line_wfs", '.wfs_search_translated',"<?php echo(url_for('gtu/gtuTranslationWfsGeom?'));?>" , data);
		  }
	  }
  }
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
			  
		$('#btn_translate_wfs').on('click',purposeTagsListWfs);
		
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

