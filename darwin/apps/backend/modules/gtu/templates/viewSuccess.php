<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'catalogue_gtu','eid'=> $form->getObject()->getId(), 'view' => true)); ?>
<?php slot('title', __('View Sampling location'));  ?>
<div class="page">
  <h1><?php echo __('View Sampling location');?></h1>
  <div class="table_view">
    <table class="classifications_edit">
      <tbody>
      <tr>
        <th><?php echo $form['code']->renderLabel().":"; ?></th>
        <td>
          <?php echo $gtu->getCode(); ?>
        </td>
      </tr>
	  <?php if($collection):?>
	  <tr>
        <th><?php echo __("Collection").":"; ?></th>
        <td>
          <?php echo $collection; ?>
        </td>
      </tr>
	  <?php endif; ?>
	   <?php if(count($date_array)>0):?>
		   <?php foreach($date_array as $date_elem):?>
		  <tr>
			<th><?php echo __("Date").":"; ?></th>
			<td>
			  <?php $from_date=$date_elem["from_date"]; $to_date=$date_elem["to_date"]; ?>
			  <?php print(html_entity_decode($from_date."-". $to_date)); ?>
			</td>
		  </tr>
		  <?php endforeach;?>		  
	  <?php endif; ?>
      <?php if($gtu->getLocation()):?>
      <!--ftheeten 2018 08 08-->
      <tr>
        <th><?php echo $form['coordinates_source']->renderLabel().":"; ?>:</th>
        <td><?php echo $gtu->getCoordinatesSource() ; ?></td>
      </tr>
      <tr>
        <th><?php echo $form['latitude']->renderLabel().":"; ?>:</th>
        <td><?php echo $gtu->getLatitude() ; ?></td>
      </tr>
      <tr>
        <th><?php echo $form['longitude']->renderLabel().":"; ?></th>
        <td><?php echo $gtu->getLongitude(); ?></td>
      </tr>
        <?php if($gtu->getCoordinatesSource()=="DMS"): ?>
        <tr>
            <th><?php echo __("DMS Latitude") ?></th>
            <td><?php echo $gtu->getLatitudeDmsDegree(); ?>°<?php echo $gtu->getLatitudeDmsMinutes(); ?>'<?php echo $gtu->getLatitudeDmsSeconds(); ?>" <?php ($gtu->getLatitudeDmsDirection()>0 ? print("N") : print("S") ); ?></td>
        </tr>
        <tr>
            <th><?php echo __("DMS Longitude") ?></th>
            <td><?php echo $gtu->getLongitudeDmsDegree(); ?>°<?php echo $gtu->getLongitudeDmsMinutes(); ?>'<?php echo $gtu->getLongitudeDmsSeconds(); ?>" <?php ($gtu->getLongitudeDmsDirection()>0 ? print("E") : print("W") ); ?></td>
        </tr>
        <?php endif; ?>
        <?php if($gtu->getCoordinatesSource()=="UTM"): ?>
        <tr>
            <th><?php echo __("UTM Northing") ?></th>
            <td><?php echo $gtu->getLatitudeUtm(); ?></td>
         </tr>
         <tr>
            <th><?php echo __("UTM Easting") ?></th>
            <td><?php echo $gtu->getLongitudeUtm(); ?></td>
         </tr>
        <?php endif; ?>
      <?php endif; ?>
      <?php if($gtu->getElevation() !== null && $gtu->getElevation() !== ''):?>
        <tr>
          <th><?php echo $form['elevation']->renderLabel().":";?></th>
          <td><?php echo $gtu->getElevation().' +- '.$gtu->getElevationAccuracy().' m'; ?></td>
        </tr>
      <?php endif;?>

      <tr>
        <th class="top_aligned">
          <?php echo __("Sampling location Tags").":" ?>
        </th>
        <td>
          <div class="inline">
            <?php echo $gtu->getName(ESC_RAW); ?>
			<div style="text-align: left">
			
		<div>
          </div>
        </td>
      </tr>
      <tr>
        <td id="refGtu" colspan="2">
          <?php //echo $gtu->getMap(ESC_RAW);
		  ?>
		  <?php if(is_numeric($gtu->getLongitude())&&is_numeric($gtu->getLatitude())):?>
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
                <input type="button" id="export-png" value="Download PNG" ></input>
                <input type="button" id="export-data" value="Download data" ></input>
    <select name="map-resolution" id="map-resolution">
    <option value="700">700 X 700</option>
    <option value="1024">1024 X 1024</option>
    <option value="2048">2048 X 2048</option>    
    </select>
    <a id="image-download" download="map.png"></a>
    <a id="data-download" download="data.txt"></a>                
			<?php endif;?>
        </td
		<!--madam 2019 01 28-->
	  </tr>
	  <tr>
		<td><?php echo image_tag('magnifier.gif');?> <?php echo link_to(__('Search related specimens'),'specimensearch/search', array('class'=>'link_to_search'));?>
<script type="text/javascript">
  $(document).ready(function (){
   
   
   if($('#gtu_temporal_information option').size()>0)
   {
        $('.counter_date').text($('#gtu_temporal_information option').size()+" Value(s)");
   }
   else
   {
         $(".date_row").hide();
   }
  
    search_data = <?php echo json_encode(array('specimen_search_filters[gtu_ref]' => $gtu->getId()));?>;
    $('.link_to_search').click(function (event){
      event.preventDefault();
      postToUrl($(this).attr('href'), search_data, true);
    });
  });
</script></td>
      </tr>
      <?php if($sf_user->isAtLeast(Users::ENCODER)):?>
      <tr>
        <td colspan="2"><?php echo image_tag('edit.png');?> <?php echo link_to(__('Edit this location'),'gtu/edit?id='.$gtu->getId());?></td>
      </tr>
      <?php endif;?>
      </tbody>
    </table>
  </div>
  <div class="view_mode">
    <?php include_partial('widgets/screen', array(
      'widgets' => $widgets,
      'category' => 'cataloguewidgetview',
      'columns' => 1,
      'options' => array('eid' => $form->getObject()->getId(), 'table' => 'gtu', 'view' => true)
    )); ?>
  </div>
</div>
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
          /*image: new ol.style.Circle({
            radius: 5,
            fill: new ol.style.Fill({color: '#ffff00'}),
            stroke: new ol.style.Stroke({color: '#000000', width: 1})*/
            image: new ol.style.Icon({
                anchor: [0.5, 46],
                anchorXUnits: 'fraction',
                anchorYUnits: 'pixels',
                src: '<?php print(public_path("images/marker-icon.png")); ?>'
              }
            
          )
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
	

    document.getElementById('export-png').addEventListener('click', function () {
      map.once('rendercomplete', function () {
        var mapCanvas = document.createElement('canvas');
        var size = map.getSize();
        mapCanvas.width = size[0];
        mapCanvas.height = size[1];
        var mapContext = mapCanvas.getContext('2d');
        Array.prototype.forEach.call(
          document.querySelectorAll('.ol-layer canvas'),
          function (canvas) {
            if (canvas.width > 0) {
              var opacity = canvas.parentNode.style.opacity;
              mapContext.globalAlpha = opacity === '' ? 1 : Number(opacity);
              var transform = canvas.style.transform;
              // Get the transform parameters from the style's transform matrix
              var matrix = transform
                .match(/^matrix\(([^\(]*)\)$/)[1]
                .split(',')
                .map(Number);
              // Apply the transform to the export map context
              CanvasRenderingContext2D.prototype.setTransform.apply(
                mapContext,
                matrix
              );
              mapContext.drawImage(canvas, 0, 0);
            }
          }
        );
        if (navigator.msSaveBlob) {
          // link download attribuute does not work on MS browsers
          navigator.msSaveBlob(mapCanvas.msToBlob(), 'map.png');
        } else {
          var link = document.getElementById('image-download');
          link.href = mapCanvas.toDataURL();
          link.click();
        }
      });
      map.renderSync();
    });
    
    document.getElementById('export-data').addEventListener('click', function () {
    var dataURL = '',  
            fieldSeparator = '\t',  
            textField = '"',  
            lineSeparator = '\n',  
            regExpTesto = /(")/g,  
            regExp = /[";]/;  
              
        dataURL=dataURL+"<?php echo $gtu->getCode(); ?>"+lineSeparator;
     <?php if($gtu->getLocation()):?>
             dataURL=dataURL+"Latitude"+fieldSeparator+"<?php echo $gtu->getLatitude(); ?>"+lineSeparator;
             dataURL=dataURL+"Longitude"+fieldSeparator+"<?php echo $gtu->getLongitude(); ?>"+lineSeparator;
        <?php endif?>
         <?php if($gtu->getCoordinatesSource()=="DMS"): ?>
            dataURL=dataURL+"Latitude DMS"+fieldSeparator+"<?php echo $gtu->getLatitudeDmsDegree(); ?>°"+"<?php echo $gtu->getLatitudeDmsMinutes(); ?>'"+"<?php echo $gtu->getLatitudeDmsSeconds(); ?>\""+"<?php ($gtu->getLatitudeDmsDirection()>0 ? print("N") : print("S") ); ?>"+lineSeparator;
            dataURL=dataURL+"Longitude DMS"+fieldSeparator+"<?php echo $gtu->getLongitudeDmsDegree(); ?>°"+"<?php echo $gtu->getLongitudeDmsMinutes(); ?>'"+"<?php echo $gtu->getLongitudeDmsSeconds(); ?>\""+"<?php ($gtu->getLongitudeDmsDirection()>0 ? print("E") : print("W") ); ?>"+lineSeparator;
          <?php endif; ?>
       <?php if($gtu->getElevation() !== null && $gtu->getElevation() !== ''):?>
        dataURL=dataURL+"Elevation"+fieldSeparator+"<?php echo $gtu->getElevation(); ?>"+lineSeparator;
       <?php endif;?>
       dataURL=dataURL+"Tags"+lineSeparator;
       <?php $tags=$gtu->getTagsArray();?>
       <?php if(count($tags)>0):?>
       <?php foreach($tags as $k=>$v):?>
            dataURL=dataURL+"<?php print($k);?>"+fieldSeparator+"<?php print($v);?>"+lineSeparator;
       <?php endforeach;?>
       <?php endif;?>
    window.location.href = 'data:text/csv;charset=utf-8;base64,' + btoa(dataURL); 
          
       
    });

    $("#map-resolution").change(
        function()
        {
             $("#map").height($("#map-resolution").val());
             $("#map").width($("#map-resolution").val());
             setTimeout( function() { map.updateSize();}, 200);
        }
    );
	<?php endif;?>
  </script>
