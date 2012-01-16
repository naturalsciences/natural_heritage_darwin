  var epsg4326;
  var markers;
  var marker;
  var center;
  var zoom = 2;
  var pointFeature;
  var point;
  var vectorLayer;
  var gsat;
  var gphy;
  var gmap;
  var ghyb;
  var mapnik;
  var style_blue;
  var map;
OpenLayers.ImgPath = "/images/ol_theme_dark/";
function initMap(mapId)
{
  epsg4326 = new OpenLayers.Projection("EPSG:4326");
  options = {
//     restrictedExtent: extent,
    controls: [
      new OpenLayers.Control.Navigation(), 
      new OpenLayers.Control.PanZoomBar(),
      new OpenLayers.Control.LayerSwitcher(),
      new OpenLayers.Control.ScaleLine() 
    ],
    numZoomLevels: 20,
    displayProjection: new OpenLayers.Projection("EPSG:4326"),
    maxExtent: new OpenLayers.Bounds(-180,-90, 180, 90),
    maxResolution: 0.3515625,
    units: "m",
    theme: '/openlayers/theme/default/style.css'
  };
  style_blue = OpenLayers.Util.extend({}, OpenLayers.Feature.Vector.style['default']);
  style_blue.strokeColor = "blue"; 
  style_blue.fillColor = "blue"; 
// OpenLayers.Layer.XYZ.

   map = new OpenLayers.Map(mapId, options);

  mapnik = new OpenLayers.Layer.OSM();
  mapnik.addOptions({wrapDateLine:true});
  map.addLayer(mapnik);
  
  vectorLayer = new OpenLayers.Layer.Vector("Simple Geometry", { displayInLayerSwitcher: false, projection: new OpenLayers.Projection("EPSG:4326")});
  map.addLayers([vectorLayer]);
    
  if(with_gmap) {
    // the SATELLITE layer has all 22 zoom level, so we add it first to
    // become the internal base layer that determines the zoom levels of the
    // map.
    gsat = new OpenLayers.Layer.Google(
        "Google Satellite",
        {sphericalMercator: true,type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22}
    );
    gphy = new OpenLayers.Layer.Google(
        "Google Physical",
        {sphericalMercator: true,type: google.maps.MapTypeId.TERRAIN, visibility: false}
    );
    gmap = new OpenLayers.Layer.Google(
        "Google Streets", // the default
        {sphericalMercator: true,numZoomLevels: 20, visibility: false}
    );
    ghyb = new OpenLayers.Layer.Google(
        "Google Hybrid",
        {sphericalMercator: true,type: google.maps.MapTypeId.HYBRID, numZoomLevels: 22, visibility: false}
    );
    map.addLayers([gsat, gphy, gmap, ghyb]);
  }
  

  markers = new OpenLayers.Layer.Markers("Markers", {
    displayInLayerSwitcher: false,
    units: "m",
    projection: "EPSG:900913"
   });
  map.addLayer(markers);

  map.zoomToMaxExtent();
  map.events.on({
    "changebaselayer": function(e) {
        if(e.object.baseLayer.name != "OpenStreetMap")
          $.get('/robots.txt?use_gmap');
      }
  
  }); 
}
  
function setZoom(e)
{
  if(pointFeature)
  {
    drawAccuracy()
  }
}

function getAccuracySize(z)
{
  if(! z)
    zoom = map.getZoom();
  else
    zoom = z;
  resolution = map.getResolutionForZoom(zoom);
  return $('#gtu_lat_long_accuracy').val() / resolution;
}

function drawAccuracy()
{
  if(marker)
  {
    vectorLayer.removeAllFeatures();
    point = new OpenLayers.Geometry.Point(marker.lonlat.lon, marker.lonlat.lat);
    style_blue.pointRadius = getAccuracySize();
    pointFeature = new OpenLayers.Feature.Vector(point, null, style_blue);
    vectorLayer.addFeatures([pointFeature]);
    vectorLayer.redraw();
  }
}

function drawLatLong()
{
  if (marker) {
    removeMarkerFromMap(marker);
  }
  lonlat = new OpenLayers.LonLat($('#gtu_longitude').val(), $('#gtu_latitude').val())
  marker = addMarkerToMap(lonlat, null);
  drawAccuracy();
}
function fetchElevation(lonlat)
{
  $.ajax({
    type: "GET",
    dataType: 'JSONP',
    url: 'http://open.mapquestapi.com/elevation/v1/getElevationProfile',
    data : {inFormat:'kvp', outFormat: 'json', latLngCollection: lonlat.lat+','+lonlat.lon, unit:'m', shapeFormat: 'raw'},
    success: function(data){
      if(data.elevationProfile[0])
        $('#gtu_elevation').val(data.elevationProfile[0].height);
      else
        $('#gtu_elevation').val(0);
    }
  });
}

function fetchPositions(lonlat, zoom)
{
  $.ajax({
  url: 'http://nominatim.openstreetmap.org/reverse',
  dataType: 'json',
  data: { lat: lonlat.lat, lon: lonlat.lon, zoom: zoom, addressdetails:1, format: 'json'  },
  success: function(data) {
    console.log(data);
  }
});

}

function setPoint( e )
{
  lonlat = getEventPosition(e).wrapDateLine();
  $('#gtu_latitude').val(lonlat.lat);
  $('#gtu_longitude').val(lonlat.lon);
  fetchElevation(lonlat);
  //fetchPositions(lonlat,map.getZoom());
  drawLatLong();
  drawAccuracy();
}

function addMarkerToMap(position, icon)
{
  var marker = new OpenLayers.Marker(position.clone().transform(epsg4326, map.getProjectionObject()), icon);
  markers.addMarker(marker);

            // create a point feature

  drawAccuracy();
  return marker;
}

function removeMarkerFromMap(marker)
{
   markers.removeMarker(marker);
}

function getEventPosition(event)
{
  return map.getLonLatFromViewPortPx(event.xy).clone().transform(map.getProjectionObject(), epsg4326);
}

function setMapCenter(center, zoom)
{
  zoom = parseInt(zoom);
  var numzoom = map.getNumZoomLevels();
  if (zoom >= numzoom) zoom = numzoom - 1;
  map.setCenter(center.clone().transform(epsg4326, map.getProjectionObject()), zoom);
}

  