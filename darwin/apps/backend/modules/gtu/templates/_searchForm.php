<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php
	$flagMenu=detect_menu_hidden();
?>


<style>
	.select2-dropdown.increasedzindexclass {
		z-index: 999999;
		}
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
	  
	   .ol-popup2 {
        position: absolute;
        background-color: white;
		color:blue;
        -webkit-filter: drop-shadow(0 1px 4px rgba(0,0,0,0.2));
        filter: drop-shadow(0 1px 4px rgba(0,0,0,0.2));
        padding: 15px;
        border-radius: 10px;
        border: 1px solid #cccccc;
        bottom: 12px;
        left: -50px;
        min-width: 280px;
      }
      .ol-popup2:after, .ol-popup2:before {
        top: 100%;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
      }
      .ol-popup2:after {
        border-top-color: white;
        border-width: 10px;
        left: 48px;
        margin-left: -10px;
      }
      .ol-popup2:before {
        border-top-color: #cccccc;
        border-width: 11px;
        left: 48px;
        margin-left: -11px;
      }
      .ol-popup-closer2 {
        text-decoration: none;
        position: absolute;
        top: 2px;
        right: 8px;
      }
      .ol-popup-closer2:after {
        content: "✖";
      }
	  
     
     
      
    
</style>
<div class="catalogue_gtu">
<?php echo form_tag('gtu/search'.( isset($is_choose) && $is_choose  ? '?is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'gtu_filter'));?>
  <div class="container">
    <table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
      <thead>       
        
        <tr>
        
          <th><?php echo $form['code']->renderLabel() ?></th>
          <th><?php echo $form['gtu_from_date']->renderLabel(); ?></th>
          <th><?php echo $form['gtu_to_date']->renderLabel(); ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>		 
          <td><?php echo $form['code']->render() ?></td>
          <td><?php echo $form['gtu_from_date']->render() ?></td>
          <td><?php echo $form['gtu_to_date']->render() ?></td>
          <td></td>
        </tr>
        <tr>
          <th colspan="4"><?php echo $form['tags']->renderLabel() ?></th>
        </tr>

        <?php echo include_partial('andSearch',array('form' => $form['Tags'][0], 'row_line' => 0));?>

        <tr class="and_row">
          <td colspan="2"></td>
          <td>
             <a href="<?php echo url_for('gtu/andSearch');?>" class="and_tag"><?php echo image_tag('add_blue.png');?></a><?php print($form['tag_boolean']->render()); ?>
          </td>
        </tr>
		<tr>		
			<td colspan="3">
				<table style="border:solid;">
				<tr> <th ><?php print(__("People")); ?>:</th></tr>
				 <tr class="tag_button_line_people">
				  <td colspan="2">
					<input type="button" id='people_switch_precise' value="<?php echo __('Precise search'); ?>" disabled>
					<input type="button" id='people_switch_fuzzy' value="<?php echo __('Fuzzy search'); ?>">
				  </td>
				</tr>
				 <tr class="tag_header_line_people">
					<th colspan="2" class="precise_people"><?php echo $form['people_ref']->renderLabel();?></th>
					<th  colspan="2"class="fuzzy_people hidden"><?php echo $form['people_fuzzy']->renderLabel();?></th>
					<th><?php echo $form['role_ref']->renderLabel();?></th>
				 </tr>
				 <tr class="tag_content_line_people">
				  <td class="precise_people" colspan="2"><?php echo $form['people_ref'];?></td>
				  <td class="fuzzy_people hidden" colspan="2"><?php echo $form['people_fuzzy'];?></td>
				  <td><?php echo $form['role_ref'];?></td> 				  
				</tr>
				</table>
			</td>
			
		</tr>
        <!--ftheeten 2018 08 08-->
        <tr>
            <th><?php echo $form['ig_number']->renderLabel() ?></th>
		  <th><?php echo $form['expedition']->renderLabel() ?></th>
           <th><?php echo __("Technical ID") ?></th>
        </tr>
        
        <td><?php echo $form['ig_number']->render() ?></td>
        <td><?php echo $form['expedition']->render() ?></td>
         <td><?php echo $form['id']->render() ?></td>
         <td><input type="button" id="last_encoded" name="last_encoded" value="<?php print(__("Last encoded")); ?>"</input> </td>
        </tr>
        <!--ftheeten 2018 08 08-->
        <tr>
		  <th><?php echo $form['collection_ref']->renderLabel() ?></th>
		  <th></th>
		   <th><?php echo $form['import_ref']->renderLabel() ?></th>
        </tr>
        <tr>
        <td><?php echo $form['collection_ref']->render() ?> All :<input type="checkbox" id="all_collections" class="all_collections" checked></td>
        <td></td>
		<td><?php echo $form['import_ref']->render() ?></td>
		</tr>
      </tbody>

      </table>

      <fieldset id="lat_long_set">
       <legend>
		<input type="button" id="but_map" value="Show/Hide map"></input>
	   </legend>
        <!--ftheeten 2018 09 28-->
		 <?php if(strpos($_SERVER["REQUEST_URI"], "with_js")):?>
			<div id="map-container" style="display:none">
		 <?php else: ?>
			<div id="map-container">
		 <?php endif;?>
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
           <!--ftheeten 2018 09 28-->
            
                <!--<div id="map_search_form" ></div>-->
				<div>
					<div style="width: 100%; height:600px" id="map_ol">
							  
					</div>
					<div id="mouse-position"></div>    
				</div>
				<div id="popup2" class="ol-popup2" style="display:none">
					<a href="#" id="popup-closer2" class="ol-popup-closer2"></a>
					<div id="popup-content2"></div>
				</div>				
				<select id="layer-select-ol" >
                       <option value="Aerial">Aerial</option>
                       <option value="AerialWithLabels" selected>Aerial with labels</option>
                       <option value="Road">Road (static)</option>
                       <option value="RoadOnDemand">Road (dynamic)</option>
					   <option value="OSM">OpenStreetMap</option>
				</select>
				<?php echo $form['wkt_search']->renderLabel();?></td><td><?php echo $form['wkt_search']->render();?>
            
            <!--<?php echo __('Show accuracy of each point');?> <input type="checkbox" id="show_accuracy" /><br /><br />-->
            <!--<div style="height:400px;width:100%" id="smap"></div>-->

            <div class="pager paging_info hidden">
              <?php echo image_tag('info2.png');?>
              <span class="inner_text"></span>
            </div>
          </div>
      </fieldset>
      <?php echo $form->renderHiddenFields();?>
      <div class="edit">
        <input class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" />
      </div>
      <div class="clear"></div>
      <script type="text/javascript">
        //initSearchMap();
		

		///////////
		//Open Layers
		var map;
		var source_draw = new ol.source.Vector({wrapX: false});
		var draw;
		var clusters;
		var iLayer=0;
		var vectorLoaded =false;
		var type_draw="";
		var json_points;
		var layerLoaded=false;
		var container = document.getElementById('popup2');
		var content = document.getElementById('popup-content2');
		var closer = document.getElementById('popup-closer2');
		var overlay;
		
		var openGtu=function(id)
		{
			window.open('<?php echo url_for("gtu/view") ;?>/id/'+ id,'_blank');
		}
			
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
		
		 function removeDarwinLayer(p_max)
		 {		
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
		
		var ol_ext_inherits = function(child,parent) {
			child.prototype = Object.create(parent.prototype);
			child.prototype.constructor = child;
		};
		
		 var getFeaturesRow=function(geoJSON)
		 {    
			console.log(geoJSON);
			$(container).hide();
			if(layerLoaded)
			{
				map.removeLayer(clusters);
			}
			
			var tmpFeatures=(new ol.format.GeoJSON()).readFeatures(jQuery.parseJSON(geoJSON), {
						dataProjection: 'EPSG:4326',
						featureProjection: 'EPSG:3857'
					});
					
			var vectorSource = new ol.source.Vector({features: tmpFeatures});
		   
			if(tmpFeatures.length>0)
			{	
			  
				var clusterSource = new ol.source.Cluster({
				  distance: 40,
				  source: vectorSource
				});
				
			  var styleCache = {};
			  var keysForClick=[];
			  clusters = new ol.layer.Vector({
				source: clusterSource,
				style: function(feature) {
				
				jQuery(feature.get('features')).each(
							function(idx, item)
							{
								console.log(item);
								keysForClick[item.get('dw_id')||'']=item.get('dw_code')||'';
							 
							}
						);
					console.log(keysForClick);
				  var size = feature.get('features').length;
				  var style = styleCache[size];
				  if (!style) {
					style = new ol.style.Style({
					  image: new ol.style.Circle({
						radius: 10,
						stroke: new ol.style.Stroke({
						  color: '#fff'
						}),
						fill: new ol.style.Fill({
						  color: '#3399CC'
						})
					  }),
					  text: new ol.style.Text({
						text: size.toString(),
						fill: new ol.style.Fill({
						  color: '#fff'
						})
					  })
					});
					styleCache[size] = style;
				  }
				  return style;
				},
				keysForClick: keysForClick
			  });


			  
				map.addLayer(clusters);
				
				layerLoaded=true;
				
			var extent = vectorSource.getExtent();
				//alert(vectorSource.getExtent());
				map.getView().fit(extent, map.getSize(),{maxZoom:10});
			   if(map.getView().getZoom()>10)
			   {
				 map.getView().setZoom(10);
			   }
			   if(map.getView().getZoom()>1)
			   {
				   map.getView().setZoom(map.getView().getZoom()-1);
			   }
			  //alert("end selection");
			  $("#map_result_form").removeClass("hidden");
			}
			else
			{
				$("#hide_map").click();
			}
		};
		  
		  function isCluster(feature) 
		  {
			  if (!feature || !feature.get('features')) { 
					return false; 
			  }
			  return feature.get('features').length >= 1;
			}
		  
		 var init_overlay=function()
		 {
			 
			overlay = new ol.Overlay(/** @type {olx.OverlayOptions} */ ({
					   element: container,
							autoPan: true,
							autoPanAnimation: {
							  duration: 250
							}
						  }));
						  

			 closer.onclick = function() {
							overlay.setPosition(undefined);
							closer.blur();
							return false;
						  };
			 map.addOverlay(overlay);
		 } 
 
		function init_map_ol()
		{
    
				mousePositionControl= new ol.control.MousePosition({
					 coordinateFormat: ol.coordinate.createStringXY(4),
					projection:'EPSPG:4326',
					className: "custom-mouse-position",
					target: document.getElementById("mouse-position"),
					undefinedHTML: "&nbsp;"
				});
				scaleLineControl = new ol.control.ScaleLine();
				
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
						imagerySet: styles[i],
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
								//finishCondition: ol.events.condition.doubleClick ,
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
						  
				
					map = new ol.Map({
						target: 'map_ol',
						layers: layers,    
						 
						view: new ol.View({                    
						  center: ol.proj.fromLonLat([0,0]),
						  zoom: 2
						}),
						controls: ol.control.defaults({
								attributionOptions: ({collapsible: false})
						}).extend([mousePositionControl, scaleLineControl, new DrawBoxControl(), new DrawPolygonControl(), new MoveMapControl(), fullScreenControl])
				});
				
				  mousePositionControl.setProjection("EPSG:4326");
			   
			   map.addLayer(OSM_layer);
			   
			  init_overlay();
						
				//select background
			  var select = document.getElementById('layer-select-ol');
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
						console.log("try");
						for (var i = 0, ii = layers.length; i < ii; ++i) {
						  layers[i].setVisible(false);
						}
						OSM_layer.setVisible(true);
					}
				}
				select.addEventListener('change', onChange);
				map.on('dblclick', function(evt) { 
		
					if(type_draw=="box")            
					{
					  
						draw.finishDrawing();
					}
				});
				
				onChange();  

			map.on('click', function(evt) {
				var coordinate = evt.coordinate;
				var hdms = ol.coordinate.toStringHDMS(ol.proj.transform(
				coordinate, 'EPSG:3857', 'EPSG:4326'));
			  var feature = map.forEachFeatureAtPixel(evt.pixel, 
							  function(feature) { return feature; });
			  if (isCluster(feature)) {
				//var popup = new ol.Overlay.Popup();
				//map.addOverlay(popup);
			 
			   var html="";
				// is a cluster, so loop through all the underlying features
				var features = feature.get('features');
				for(var i = 0; i < features.length; i++) {
				  // here you'll have access to your normal attributes:
				  //console.log(features[i].get('name'));
				  <?php if(strpos($_SERVER["REQUEST_URI"], "with_js")):?>
				  html+="<a onclick=\"openGtu('"+(features[i].get('dw_id')||'')+"')\" '><u>"+(features[i].get('dw_code')||'') +" - "+(features[i].get('dw_text')||'')+ "</u></a>"+"&nbsp;<input type=\"button\" class=\"choose_gtu_in_map\" dw_id=\""+(features[i].get('dw_id')||'')+"\" value=\"choose\"><br/><br/>";
				  <?php else:?>
				   html+="<a onclick=\"openGtu('"+(features[i].get('dw_id')||'')+"')\" '><u>"+(features[i].get('dw_code')||'') +" - "+(features[i].get('dw_text')||'')+ "</u></a><br/><br/>";
				  <?php endif; ?>
				  
				}
			   
				 content.innerHTML = '<p>' + html +'</p>';
					overlay.setPosition(coordinate);
					$(container).show();
			  } 
			});						
				
		}
		
		
		
		init_map_ol();
		
		/////////////
        $(document).ready(function () {
		
		$('.select2_people').select2({
			 width: "500px",
			 multiple:true, 
			 dropdownCssClass: "increasedzindexclass",
			 ajax: {
					url: '<?php echo(url_for('catalogue/completeName'));?>',
					data: function (params) {
						//console.log(params);
					  var query = {
						table:"people",
						term: params.term,
						
					  }

					 
					  return query;
					},
						  
					processResults: function(data) {
						   var myResults = [];
							$.each(data, function (index, item) {
								myResults.push({
									'id': item.value,
									'text': item.label
								});
							});
							return {
								results: myResults
							};
						}
				},
				
		});
			
				
		$(".but_more").click(
			function()
			{
				if($('#all_collections:checked').length>0)
				{
					$("#all_collections").click();
				}
			}
		);
		
		

        
       $("#all_collections").change(
            function()
            {
                if(this.checked)
                {
                    oldCollId=$(".collection_ref").val();
                    $(".collection_ref").prop('disabled', true);
                    $(".collection_ref").val("/");
                }
                else
                {
                    $(".collection_ref").prop('disabled', false);
                    $(".collection_ref").val(oldCollId);
                }
            }
       
       );
       // onload
        $('#all_collections').prop('checked', true);
        $(".collection_ref").prop('disabled', true);
        $(".collection_ref").val("/");
        
          $('.catalogue_gtu').choose_form({});

          $(".new_link").click( function() {
            url = $(this).find('a').attr('href');			
			data= $(".gtu_code_callback[value!='']").serialize();			
            reg=new RegExp("(<?php echo $form->getName() ; ?>)", "g");
            open(url+'?'+data.replace(reg,'gtu'));
            return false;
          });


          var num_fld = 1;
          $('.and_tag').click(function()
          {
            hideForRefresh('#gtu_filter');
            $.ajax({
                type: "GET",
                url: $(this).attr('href') + '/num/' + (num_fld++) ,
                success: function(html)
                {
                  $('table.search > tbody .and_row').before(html);
                  showAfterRefresh('#gtu_filter');
                }
            });
            return false;

          });
		  
		  //ftheeten 2019 01 29
		 <?php if(array_key_exists("name", $_REQUEST)): ?>
				if($(".gtu_code_callback").length)
				{
					
					$(".gtu_code_callback").val("<?php print($_REQUEST["name"]);?>");
				}
			   <?php endif;?>
               
           $("#last_encoded").click(
            function()
            {
                var url_last="<?php echo(url_for('gtu/getLastEncodedId?'));?>";
                 $.getJSON(url_last, {                                
                            } , 
                            function (data) 
                            {
                                $(".gtu_id_callback").val(data.id);
                                $(".search_submit").click();
                                //onElementInserted("body", ".result_choose", function(){$(".result_choose").click();} )
                            });
                   
            }
           );  

        //ftheeten 2018 04 10
                  var ig_num=urlParam('ig_num');
                  if(!!ig_num)
                  {
                    
                        $("#gtu_filters_ig_number").val(decodeURIComponent(ig_num));
                        $( ".search_form" ).submit();
                  } 


//people ctrl part
		
		$('#people_switch_precise').click(function() {

			$('#people_switch_precise').attr('disabled','disabled') ;
			$('#people_switch_fuzzy').removeAttr('disabled') ;
			$('.fuzzy_people').hide();
			$('.precise_people').show();
			$(this).closest('table').find('.people_switch_fuzzy').toggle() ;
		   
			check_state();
			// $('#specimen_search_filters_Peoples_people_ref_name').html("") ;
			// $('#specimen_search_filters_Peoples_people_ref').val("") ;
		  });

		  $('#people_switch_fuzzy').click(function() {

			$('#people_switch_precise').removeAttr('disabled') ;
			$('#people_switch_fuzzy').attr('disabled','disabled') ;
			$('.fuzzy_people').show();
			$('.precise_people').hide();
			$('.fuzzy_people').find('input:text').val("") ;
			check_state();
		  });
		  
		   if($('.class_fuzzy_people').val() != '')
		  {
			tmpVal=$('.class_fuzzy_people').val();
			$('#people_switch_fuzzy').trigger("click") ;
			$('.class_fuzzy_people').val(tmpVal);
		  }		

			$("#but_map").click(
				function()
				{
					$("#map-container").toggle();
					map.updateSize();
				}
			);

        });
        
                 

            
          //ftheeten 2018 03 08
          var url="<?php echo(url_for('catalogue/expeditionsAutocomplete?'));?>";
          var autocomplete_rmca_array=Array();
          $('.autocomplete_for_expeditions').autocomplete({
                source: function (request, response) {
                    $.getJSON(url, {
                                term : request.term
                            } , 
                            function (data) 
                                {
                            response($.map(data, function (value, key) {
                            return value;
                            }));
                    });
                },
                minLength: 2,
                delay: 200
            });
			


      </script>
      <div class="search_results">
        <div class="search_results_content"></div>
      </div>
      <?php if($flagMenu):?><div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for('gtu/new') ?>"><?php echo __('New');?></a></div><?php endif;?>
  </div>
</form> 
</div>
