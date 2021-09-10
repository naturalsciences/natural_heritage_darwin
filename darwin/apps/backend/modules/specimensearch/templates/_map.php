<style>
      p.collapse{
         display:none;
      }
      
      .ol-popup {
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
      .ol-popup:after, .ol-popup:before {
        top: 100%;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
      }
      .ol-popup:after {
        border-top-color: white;
        border-width: 10px;
        left: 48px;
        margin-left: -10px;
      }
      .ol-popup:before {
        border-top-color: #cccccc;
        border-width: 11px;
        left: 48px;
        margin-left: -11px;
      }
      .ol-popup-closer {
        text-decoration: none;
        position: absolute;
        top: 2px;
        right: 8px;
      }
      .ol-popup-closer:after {
        content: "✖";
      }
      


.ul_logo
{
    margin-top:5%;
    vertical-align: bottom;
    text-align: center;
}
.ul_logo li
{
    display:inline-block;
    margin-right:30px;
    margin-top:30px;
}

</style>

<div class="widget" id="map_result_form" >
  <div  id="show_map"  style="background-color:#5BAABD" class="widget_top_button map_selector hidden" /></div><div id="hide_map" style="background-color:#5BAABD" class="widget_bottom_button  map_selector"></div>
   <div class="widget_content">
	   <div style="width: 100%; height:500px; display:inline-block" id="smap">
	  
	  </div>
	   <div id="mouse-position"></div>
		<div id="popup" class="ol-popup">
			  <a href="#" id="popup-closer" class="ol-popup-closer"></a>
			  <div id="popup-content"></div>
		 </div>
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
			<select id="layer-select">
						   <!---<option value="Aerial">Aerial</option>
						   <option value="AerialWithLabels" selected>Aerial with labels</option>
						   <option value="Road">Road (static)</option>
						   <option value="RoadOnDemand">Road (dynamic)</option>-->
						   <option value="OSM">OpenStreetMap</option>
						   <option value="esri_satelite">ESRI Web service</option>
						   
			</select>
			 <input type="button" id="export-png" value="Download PNG" ></input>
				<select name="map-resolution" id="map-resolution">
				<option value="700">700 X 700</option>
				<option value="1024">1024 X 1024</option>
				<option value="2048">2048 X 2048</option>    
				</select>
				<a id="image-download" download="map.png"></a>
		</div>
    
</div>




<script  type="text/javascript">
var map;
var mousePositionControl;
var scaleLineControl;
var json_points='<?php print(html_entity_decode($geojson));?>';
var clusters;
var layerLoaded=false;
var container = document.getElementById('popup');
var content = document.getElementById('popup-content');
var closer = document.getElementById('popup-closer');
var overlay;
var wfs_url="<?php print(sfConfig::get('dw_root_url_wfs'));?>";
var globalLayers=Array();
var displayed=true;

var ol_ext_inherits = function(child,parent) {
		child.prototype = Object.create(parent.prototype);
		child.prototype.constructor = child;
	};
	
var openSpecimen=function(id)
{
	window.open('<?php echo url_for("specimen/view") ;?>/id/'+ id,'_blank');
}
//main search function
 var getFeaturesRow=function(geoJSON)
 {    
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
                        keysForClick[item.get('id')||'']=item.get('code')||'';
                     
                    }
                );
               
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
  
  function isCluster(feature) {
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
 
 var parseCapabilities= function(wms_point)
	{
		var cap_query=wfs_url + wms_point+ '/ows?service=wms&version=1.1.1&request=GetCapabilities';
		
		$.get( cap_query)
			  .done(function( data ) {
					
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
	
  $("#put_layer").click(
		function()
		{
			var wms_url=$("#addwmslayer").val();					
            addLayer($("#addwms").val(),$("#addwmslayer").val());
		}
	);
   
	
 var init_map=function(){
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
	mousePositionControl= new ol.control.MousePosition({
			 coordinateFormat: ol.coordinate.createStringXY(4),
			projection:'EPSPG:4326',
			className: "custom-mouse-position",
			target: document.getElementById("mouse-position"),
			undefinedHTML: "&nbsp;"
		});
	scaleLineControl = new ol.control.ScaleLine();
	 var fullScreenControl = new ol.control.FullScreen();
	 
	      
	 
	map = new ol.Map({
				target: 'smap',
				layers: layers,    
				 
				view: new ol.View({                    
				  center: ol.proj.fromLonLat([0,0]),
				  zoom: 2
				}),
				controls: ol.control.defaults({
						attributionOptions: ({collapsible: false})
				}).extend([mousePositionControl, scaleLineControl,  fullScreenControl])
		});
	map.addLayer(OSM_layer);
	init_overlay();
    mousePositionControl.setProjection("EPSG:4326");
	
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
				  html+="<a onclick=\"openSpecimen('"+(features[i].get('dw_id')||'')+"')\" '><u>"+(features[i].get('dw_code')||'')+" "+ (features[i].get('dw_taxon_name')||'') +"</u></a>"+"<br/>";
				  
				}
			   
				 content.innerHTML = '<p>' + html +'</p>';
					overlay.setPosition(coordinate);
			  } 
			});		
}



$(document).ready(
	function()
	{
		init_map();
		getFeaturesRow(json_points);
		
		$("#browse_wms").click(
			function()
			{
				var wms_url=$("#addwms").val();					
				parseCapabilities(wms_url);
				current_wms=wms_url;
			}
		);
		
		
		
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
    


    $("#map-resolution").change(
        function()
        {
             $("#smap").height($("#map-resolution").val());
             $("#smap").width($("#map-resolution").val());
             setTimeout( function() { map.updateSize();}, 200);
        }
    );
		
	}
);
</script>