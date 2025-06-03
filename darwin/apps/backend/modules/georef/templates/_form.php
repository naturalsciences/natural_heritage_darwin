<form id="georef_form" class="georef_form">
<table>
	<tr><td><?php echo $form['id']->renderLabel() ;?></td><td><?php echo $form['id'] ;?></td></tr>
	<tr><td><?php echo $form['source']->renderLabel() ;?></td><td><?php echo $form['source'] ;?></td></tr>
	<tr><td><?php echo $form['name']->renderLabel() ;?></td><td><?php echo $form['name'] ;?></td></tr>
	<tr><td><?php echo $form['country']->renderLabel() ;?></td><td><?php echo $form['country'] ;?></td></tr>
	<tr><td><?php echo $form['country_iso3166']->renderLabel() ;?></td><td><?php echo $form['country_iso3166'] ;?></td></tr>
	<tr><td><?php echo $form['osm_id']->renderLabel() ;?></td><td><?php echo $form['osm_id'] ;?></td></tr>
</table>
<br/>
<input type="button" id="query_nominatim" value="Query Nominatim"></input>
<select  id="nominatim_results"></select> <input type="button" id="display_nominatim" value="display Nominatim"></input>
<br/>

<div>
		<div>
					<div style="width: 700px; height:700px" id="map">
							  
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
</div>
<table>
<tr><th><?php echo $form['osm_json']->renderLabel() ;?></th></tr>
<tr><td><?php echo $form['osm_json'];?></td></tr>
</table>


<div  id="linked_item_list">
		<?php include_partial('linked_itemlist',array('linked_items'=>$linked_items, 'form'=> $form, 'mode'=> $mode));?>
</div>

 <div class="form_buttons">
 <?php if($mode=="edit"): ?>
<a href="<?php echo url_for('gtu/choosePinned') ?>" id="add_multiple_pin"><?php echo __('Add multiple items');?></a>
<?php endif;?>
<input type="button" id="record_nominatim" value="Record"></input>
</div>
</form>


<script language="javascript">
	var map;
	var mousePositionControl;
	var scaleLineControl;
	var OSM_layer;
	
	var query_url_nominatim="https://nominatim.openstreetmap.org/search?format=json&extratags=1&polygon_geojson=1";
	var url_post_nominatim="<?php print(url_for("georef/saveNominatimGeoRef")); ?>?";
	
	var nominatim_results=Array();
	
	var nominatim_layer= null;
	var nominatim_loaded=false;
	
	var current_osm= null;
	var metadata_osm= null;
	var geojson_osm=null;
	
	var max_zoom=16;
	
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
			

	function addNominatimLayer( p_geojson)
        {

			geojson_osm=p_geojson;
              
			if(nominatim_loaded)
			{
				map.removeLayer(nominatim_layer);
			}
			var tmp_features=  (new ol.format.GeoJSON( { dataProjection: "EPSG:4326", featureProjection: "EPSG:3857" } )).readFeatures(
			p_geojson, 
			)
			
            var tmpSource=new ol.source.Vector(
				{
					  features: tmp_features,
					  
				}
			);
            
             nominatim_layer = new ol.layer.Vector({
                       
                        source: tmpSource,
                        style: styleWKT	,
						
						
						});
                        
            
            map.addLayer(nominatim_layer);
			
			var extent = nominatim_layer.getSource().getExtent();
			map.getView().fit(extent, map.getSize());
            if(map.getView().getZoom()>max_zoom)
			{
				map.getView().setZoom(max_zoom);
			}
            nominatim_loaded=true;		
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
		  
		
       		map = new ol.Map({
				target: 'map',
				layers: layers,    
				 
				view: new ol.View({                    
				  center: ol.proj.fromLonLat([0,0]),
				  zoom: 5
				}),
				controls: ol.control.defaults({
						attributionOptions: ({collapsible: false})
				}).extend([mousePositionControl, scaleLineControl])
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
				console.log("try");
				for (var i = 0, ii = layers.length; i < ii; ++i) {
				  layers[i].setVisible(false);
				}
				OSM_layer.setVisible(true);
			}
		}
		select.addEventListener('change', onChange);
		onChange();   
	}

	
	var display_nominatim=function(p_json)
	{
		nominatim_results=p_json;
		var data_length = p_json.length;
		$('#nominatim_results').empty();
		for (var i = 0; i < data_length; i++) 
		{
			var tmp=nominatim_results[i];
			$('#nominatim_results').append($('<option>', {
				value: i,
				text: ((tmp["osm_id"].toString() ??"")+" "+(tmp["display_name"]??"")+" "+(tmp["place"]??"")+" "+(tmp["type"]??"")).trim()
			}));
		}
	}
	
	$("#query_nominatim").click(
		function()
		{
			nominatim_results=Array();
			console.log("query nominatim");
			var name=$(".georef_name").val();
			var country_iso3166=$(".georef_country_iso3166").val();
			if(name!==undefined)
			{
				if(name.length>0)
				{
					var url=query_url_nominatim+"&q="+encodeURI(name);
					if(country_iso3166.length>0)
					{
						url=url+"&countrycodes="+encodeURI(country_iso3166);
					}
					console.log(url);
					$.getJSON(url,
						function(result)
						{
							display_nominatim(result)
							
						}
					
					)
				}
				
			}
			
		}
		
	);
	
	var add_nominatim_metadata=function(p_metadata_osm)
	{
		if("geojson" in p_metadata_osm)
		{
			delete p_metadata_osm["geojson"];
		}
		console.log(p_metadata_osm);
		$(".osm_json").val(JSON.stringify(p_metadata_osm));
	}
	
	$("#metadata_osm").change(
		function()
		{
			current_osm=null;
			metadata_osm=null;
			geojson_osm=null;
		}
	);
	
	$("#display_nominatim").click(
		function()
		{
			current_osm=null;
			metadata_osm=null;
			geojson_osm=null;
			
			if(nominatim_results.length>0)
			{
				var current_osm_idx=$( "#nominatim_results option:selected" ).val();
				console.log(current_osm_idx);
				current_osm=nominatim_results[current_osm_idx];
				metadata_osm={};
				Object.assign(metadata_osm, current_osm);
				
				
				add_nominatim_metadata(metadata_osm);
				var current_geojson=current_osm["geojson"];
				console.log(current_geojson);
				addNominatimLayer(current_geojson);
			}
		}
	)
	
	$("#record_nominatim").click(
		function()
		{
			var osm_id="";
			var osm_name=""
			var osm_geotype="";
			var osm_class="";
			var osm_type="";
			var osm_place_rank="";
			
			if(metadata_osm!==null)
			{
				if("osm_id" in metadata_osm)
				{
					osm_id=metadata_osm["osm_id"];
				}
				
				if("name" in metadata_osm)
				{
					osm_name=metadata_osm["name"];
				}
				
				if("osm_geotype" in metadata_osm)
				{
					osm_geotype=metadata_osm["osm_geotype"];
				}
				
				if("osm_class" in metadata_osm)
				{
					osm_class=metadata_osm["osm_class"];
				}
				
				
				if("osm_type" in metadata_osm)
				{
					osm_type=metadata_osm["osm_type"];
				}
				
				if("osm_place_rank" in metadata_osm)
				{
					osm_place_rank=metadata_osm["osm_place_rank"];
				}
			
				console.log(osm_id);
				
				$.ajax({
				  type: "POST",
				  url: url_post_nominatim,
				  data: {
					metadata_osm:JSON.stringify(metadata_osm),
					geojson_osm:JSON.stringify(geojson_osm),
					osm_id:osm_id,
					osm_name: osm_name,
					osm_geotype:osm_geotype,
					osm_class:osm_class,
					osm_type:osm_type,
					osm_place_rank:osm_place_rank
				  },
				  success: function(data)
							{
								console.log(data);
							}
				  ,
				  dataType: "json"
				});
			}
			 <?php if($mode=="edit"): ?>
			 var id=<?php print($form->getObject()->getId()); ?>;
			 var url_create_object="<?php print(url_for("georef/createGeorefGtuLink")); ?>?";
			 
			 $(".linked_gtu").each(function()
              {
                  console.log($(this).val());
				  $.ajax({
				  type: "GET",
				  url: url_create_object,
				  data: {
					"georef_ref":id,
					"gtu_ref":$(this).val(),
				  },
				  success: function(data)
							{
								console.log(data);
							}
				  ,
				  dataType: "json"
				});
              });
			 <?php endif; ?>
		}
	
	);
	
	//launch
	
	var init_linked_gtu=function(p_id)
	{
		var url_display_object="<?php print(url_for("georef/getExistingGtuToGeoref")); ?>?";
		 $.ajax({
				  type: "GET",
				  url: url_display_object,
				  data: {
					"georef_ref":p_id,
					
				  },
				  success: function(data)
							{
								console.log(data);
								if("returned" in data)
								{
									for(var i=0; i< data["returned"].length;i++)
									{
											console.log(data["returned"][i]["gtu_ref"]);
											console.log('<?php echo url_for('georef/AddGtuToGeoref?georef_ref='); ?>'+ p_id+'/num/' + ( 1+$(".main_line_georef").length)+'/gtu_ref/'+data['returned'][i]["gtu_ref"]+"/georef_ref_id/"+data['returned'][i]["id"]);
											$.ajax(
											{
												
											  
											   async: false,
											  type: "GET",
											 url: '<?php echo url_for('georef/AddGtuToGeoref?georef_ref='); ?>'+ p_id+'/num/' + ( 1+$(".main_line_georef").length)+'/gtu_ref/'+data['returned'][i]["gtu_ref"]+"/georef_ref_id/"+data['returned'][i]["id"],
											  success: function(html)
											  {
												ref_table = $('.list_gtu > tbody');
												ref_table.append(html);
												$('.warn_message').addClass('hidden');
												showAfterRefresh('.georef_form');
												$('.georef_form').css("z-index",999);
												$('.georef_form > table').removeClass('hidden');
												
											  }
											});
									}	
								}
							}
				  ,
				  dataType: "json"
				});
	}
	
$(document).ready(
	
		function()
		{
			init_map();
			
			<?php if($mode=="edit"): ?>
				<?php if(strlen($geojson)>0): ?>
					var url_get_json="<?php print(url_for("georef/get_json")); ?>?id=<?php print($form->getObject()->getId()) ;?>";
					console.log(url_get_json);
					$.ajax({
						  type: "GET",
						  url: url_get_json,
						  data: {
							
						  },
						  success: function(data)
									{
										console.log(data);
										if("geojson" in data)
										{
											addNominatimLayer(data["geojson"])
											
										}
									}
						  ,
						  dataType: "json"
						});
				<?php endif;?>
				init_linked_gtu(<?php print($form->getObject()->getId()) ;?>);
			<?php endif;?>
			
			 
			 
			   
			   
			     function addPinned(gtu_id, gtu_name)
				  {
					info = 'ok';
					ref_table = $('.list_gtu > tbody');
					ref_table.find('tr').each(function() {
					  if($(this).find('input[id$=\"_gtu_ref\"]').val() === gtu_id) info = 'bad' ;
					});
					if(info != 'ok') return false;
					hideForRefresh('.georef_form') ;
					console.log($(".main_line_georef").length);
					console.log(gtu_id);
					$.ajax(
					{
						
					  //ftheeten 2016 06 09 (because issue with "$(ref_table).find('tr').length" on series)
					   async: false,
					  type: "GET",
					 url: '<?php echo url_for('georef/AddGtuToGeoref?georef_ref='.$form->getObject()->getId()) ?>'+ '/num/' + ( 1+$(".main_line_georef").length)+'/gtu_ref/'+gtu_id,
					  success: function(html)
					  {
						ref_table.append(html);
						$('.warn_message').addClass('hidden');
						showAfterRefresh('.georef_form');
						$('.georef_form').css("z-index",999);
						$('.georef_form > table').removeClass('hidden');
						
					  }
					});
					return true;
				  }
			     $(".georef_form").catalogue_people({add_button: '#add_multiple_pin', q_tip_text: 'Choose Darwin Item',update_row_fct: addPinned });
			
			var delete_georef_ref=function(url_delete, row_html)
			{
				console.log("delete");
				$.ajax(
					{
						
					  //ftheeten 2016 06 09 (because issue with "$(ref_table).find('tr').length" on series)
					   async: false,
					  type: "GET",
					 url: url_delete,
					  success: function(html)
					  {
						
					
						row_html.empty();
						showAfterRefresh('.georef_form');
						$('.georef_form').css("z-index",999);
						
						
					  }
					});
			}
			
			$("body").on("click","a.row_delete_lk_georef",
				function(e)
				{
					console.log("click");
					var id_rel=$(this).attr("id_rel_dw");
					console.log(id_rel);
					var row=$(this).closest(".main_line_georef");
					
					
					var url_delete_ref="<?php print(url_for("georef/delete_relation")); ?>?id="+id_rel;
					delete_georef_ref(url_delete_ref, row);
					 e.preventDefault();
				}
			);
		
		}
		
	);

</script>