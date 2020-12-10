<!--link to OpenLayers 3 ftheeten 2018 06 04-->
 <?php if(isset($gtu)):?>
<script language="JavaScript" type="text/javascript" src="<?php print(public_path('/openlayers/v4.x.x-dist/ol.js'));?>"></script>
<link rel="stylesheet" href="<?php print(public_path('/openlayers/v4.x.x-dist/ol.css'));?>">
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
    <?php if(isset($gtu) && ($spec->getStationVisible() || (!$spec->getStationVisible() && $sf_user->isAtLeast(Users::ENCODER)))) : ?>
    <tr>
      <th><label><?php echo __('Sampling location code');?></label></th>
      <td id="specimen_gtu_ref_code">
        <?php if($sf_user->isAtLeast(Users::ENCODER)):?>
          <?php echo link_to($gtu->getCode(), 'gtu/view?id='.$spec->getGtuRef(), array('target' => '_blank')) ?>
        <?php else:?>
          <?php echo $gtu->getCode();?>
        <?php endif;?>
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
                       <option value="Aerial">Aerial</option>
                       <option value="AerialWithLabels" selected>Aerial with labels</option>
                       <option value="Road">Road (static)</option>
                       <option value="RoadOnDemand">Road (dynamic)</option>
					   <option value="OSM">OpenStreetMap</option>
				</select>	
			<?php endif;?>
		<div>
      </td>
    </tr>
	<!--addition ftheeten 2014-->
	 <tr>
        <th class="top_aligned">
          <?php echo __("Other information") ?>
        </th>
        <td>
			 <div class="inline">
				<?php 
					$tmpComments = Doctrine::getTable('Comments')->findForTable('gtu',$gtu->getId());
					
						$flagGo=TRUE;
						
						
						$nbr = count($tmpComments);
						if(! $nbr) 
						{
							echo "-";
							$flagGo=True;
						}
						if($flagGo===TRUE)
						{
							$str = '<ul  class="search_tags">';
								foreach($tmpComments as $valC)
								{
								 
									$str .= '<li><label>Comment<span class="gtu_group"> - '.$valC->getNotionConcerned().'</span></label><ul class="name_tags'.($view!=null?"_view":"").'">';
									$str .=  '<li>' . trim($valC->getComment()).'</li>';
									$str .= '</ul><div class="clear"></div>';
									
								  
								}
								$str .= '</ul>';
							echo $str;
						}
				?>
			</div>
		</td>
      </tr>	  
	<!--end addition ftheeten 2014-->
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
		
		
		
		function init_map(){
    
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

		  
		//layers[layers.length]=layer_point;
     
       		map = new ol.Map({
				target: 'map',
				layers: layers,    
				 
				view: new ol.View({                    
				  center: ol.proj.fromLonLat([<?php print($gtu->getLongitude());?>,<?php print($gtu->getLatitude());?>]),
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
	
	<?php endif;?>
  </script>
</table>
 <?php endif;?>
