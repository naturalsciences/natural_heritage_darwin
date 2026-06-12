<!--link to OpenLayers 3 ftheeten 2018 06 04-->
 <?php if(isset($gtu)):?>
<table class="catalogue_table_view">
  <tbody>
    <tr>
      <th>
        <?php echo __('Station visible ?') ?>
      </th>
      <td>
        <?php echo $spec->getStationVisible()?__("Yes"):__("No") ; ?>
      </td>
    </tr>
    <?php /*ftheeten 2018 10 31*/ if(is_object($gtu)):?>
    <?php if(isset($gtu) && ($spec->getStationVisible() || (!$spec->getStationVisible() && $sf_user->isAtLeast(Users::ENCODER)))) : ?>
    <tr>
      <th><label><?php echo __('Sampling location code');?></label></th>
      <td>
        <?php echo link_to($gtu->getCode(), 'gtu/view?id='.$spec->getGtuRef()) ?>
      </td>
    </tr>
    <?php if($gtu->getLocation()):?>
    <tr>
      <th><label><?php echo __('Latitude');?></label></th>
      <td id="specimen_gtu_ref_lat"><?php echo $gtu->getLatitude() ; ?></td>
    </tr>
    <tr>
      <th><label><?php echo __('Longitude');?></label></th>
      <td id="specimen_gtu_ref_lon"><?php echo $gtu->getLongitude(); ?></td>
    </tr>
    <?php endif;?>
    <?php if($gtu->getElevation()):?>
    <tr>
      <th><label><?php echo __('Altitude');?></label></th>
      <td id="specimen_gtu_ref_elevation"><?php echo $gtu->getElevation().' +- '.$gtu->getElevationAccuracy().' m'; ?></td>
    </tr>
    <?php endif;?>
    <tr>
      <th><label><?php echo __('Date from');?></label></th>
      <td id="specimen_gtu_date_from" class="datesNum"><?php echo $spec->getTemporalInformation()->getFromDateMasked(ESC_RAW);?></td>
    </tr>
    <tr>
      <th><label><?php echo __('Date to');?></label></th>
      <td id="specimen_gtu_date_to" class="datesNum"><?php echo $spec->getTemporalInformation()->getToDateMasked(ESC_RAW);?></td>
    </tr>
    <tr>
      <th class="top_aligned">
        <?php echo __("Sampling location Tags") ?>
      </th>
      <td>
        <div class="inline">
          <?php echo $gtu->getName(ESC_RAW); ?>
        </div>
      </td>
    </tr>
    <tr>
      <td colspan="2" id="specimen_gtu_ref_map">
        <?php 
		
		//echo $gtu->getMap(ESC_RAW);
		
		?>
		<div style="text-align: left">
			<?php if(is_numeric($gtu->getLongitude())&&is_numeric($gtu->getLatitude())):?>
			<div>
					<div style="width: 600px; height:400px" id="map">
							  
					</div>
					<div id="mouse-position"></div>    
				</div>  
				<select id="layer-select" >
                        <option value="OSM" selected>OpenStreetMap</option>
                       <option value="World_Imagery">ESRI Image service</option>
					   <option value="World_Topo_Map">ESRI World topo map</option>
					   
				</select>	
				<br/>
				<form id="frm_rel_gis" target="_blank" action="<?php print(url_for("specimensearch/search/1")); ?>" method="post">
				 View other records located nearby (radius) :<select id="related_radius" class="related_radius" name="related_radius" >
					<option value="0"></option>
					<option value="10">10</option>
					<option value="20">20</option>
					<option value="30">30</option>
					<option value="50">50</option>
					<option value="100">100</option>
					<option value="500">500</option>
					<option value="1000">1000</option>
					<option value="2000">2000</option>
					<option value="5000">5000</option>
					<option value="10000">10000</option>
					</select>m.
					<input type="hidden" id="rel_wkt" name="specimen_search_filters[wkt_search]"></input>
					<input type="submit"></input>
					
				</form>
			<?php endif;?>
		<div>
      </td>
    </tr>
    <?php if (
      isset($commentsGtu) &&
      count($commentsGtu) != 0
      ): ?>
	  <tr id="specimen_gtu_related_info">
      <th>
        <?php echo __("Related comments") ?>
      </th>
      <td class="top_aligned">
        <?php use_helper('Text');?>
        <?php foreach($commentsGtu as $comment):?>
          <fieldset class="opened view_mode"><legend class="view_mode"><b><?php echo __('Notion');?></b> : <?php echo __($comment->getNotionText());?></legend>
            <?php echo auto_link_text( nl2br($comment->getComment())) ;?>
          </fieldset>
        <?php endforeach ; ?>
      </td>
    </tr>
    <?php endif; ?>
    <?php elseif(isset($gtu) && $gtu->hasCountries()):?>
    <tr>
      <th class="top_aligned">
        <?php echo __("Sampling location countries") ?>
      </th>
      <td>
        <div class="inline">
          <?php echo $gtu->getRawValue()->getName(null, true); ?>
        </div>
      </td>
    </tr>
    <?php endif ; ?>
  </tbody>
  <script  type="text/javascript">
	<?php if(is_numeric($gtu->getLongitude())&&is_numeric($gtu->getLatitude())):?>
var mousePositionControl;
		var scaleLineControl;
		var map;
		var featuresPoint = new Array();
		var OSM_layer;
		var styles =["World_Imagery", "World_Topo_Map"];
		var layers = [];
		var center_point=null;
		var center_point_buffered=null;
		var center_point_buffered_4326=null;
		var center_point_buffered_4326_wkt=null;
		var radius_layer=null;
		var init_radius=false;
		
		
		
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
		
		
		for (i = 0, ii = styles.length; i < ii; ++i) {
			layers.push(new ol.layer.Tile({
			  visible: false,
			  preload: Infinity,
			  source: new ol.source.XYZ({
						url:
			  'http://server.arcgisonline.com/ArcGIS/rest/services/'+styles[i]+'/MapServer/tile/{z}/{y}/{x}',
	
			  maxZoom:12
					})
			}));
		}
	   OSM_layer = new ol.layer.Tile({
		    visible: false,
            source: new ol.source.OSM()
          });
		
			 
		 var wkt = 'POINT(<?php print($gtu->getLongitude());?> <?php print($gtu->getLatitude());?>)';

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

		  center_point=[<?php print($gtu->getLongitude());?>,<?php print($gtu->getLatitude());?>];
		//layers[layers.length]=layer_point;
     
       		map = new ol.Map({
				target: 'map',
				layers: layers,    
				 
				view: new ol.View({                    
				  center: ol.proj.fromLonLat(center_point),
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
		function onChange() 
		{
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
	init_map();
	
	$(document).ready(
		function()
		{
			$("#related_radius").change(
				function()
				{
					console.log("change_radius");
					var radius=$(this).val();
					console.log(radius);
					draw_radius(center_point, radius);
				}
			)
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
			
			var draw_radius=function(pt_4326, radius_m)
			{
				radius_m=parseInt(radius_m);
				if(init_radius)
				{
					map.removeLayer(radius_layer);
				}
				if(radius_m!=0)
				{
					console.log(pt_4326)
					//var tranformed=ol.proj.transform(pt_4326, 'EPSG:4326','EPSG:3857');		
					center_point_buffered =ol.geom.Polygon.circular(pt_4326, radius_m, 64);
					center_point_buffered_4326=	center_point_buffered.clone()			
					center_point_buffered.transform('EPSG:4326','EPSG:3857');				
					var format = new ol.format.WKT();						
					center_point_buffered_4326_wkt=format.writeGeometry(center_point_buffered_4326);	
					var  generic_feature = new ol.Feature({geometry: center_point_buffered});
					var tmpSource=new ol.source.Vector();
						tmpSource.addFeature(generic_feature);
						
						radius_layer = new ol.layer.Vector({								
									source: tmpSource,
									style: styleWKT	});
									
						
						map.addLayer(radius_layer);
						$("#rel_wkt").val(center_point_buffered_4326_wkt);
						//map.getView().fit(radius_layer.getSource().getExtent());
						init_radius=true;
				}
			}
			
		}
		
		
	);
	<?php endif;?>
    <?php endif;?>
  </script>
</table>
  <?php elseif(is_numeric($spec->getTemporalInformation()->getId())): ?>
  <?php $dateTmp=$spec->getTemporalInformation();?>
  <table>
   <tr>
      <th><label><?php echo __('Date from');?></label></th>
      <td id="specimen_gtu_date_from" class="datesNum"><?php echo $dateTmp->getFromDateMasked(ESC_RAW);?></td>
    </tr>
    <tr>
      <th><label><?php echo __('Date to');?></label></th>
      <td id="specimen_gtu_date_to" class="datesNum"><?php echo $dateTmp->getToDateMasked(ESC_RAW);?></td>
    </tr>
  </table>
 <?php endif;?>