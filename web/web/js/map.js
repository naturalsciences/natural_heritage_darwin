L.Icon.Default.imagePath = '/leaflet/images/';
var map;
var marker;
var accuracy;
function initEditMap(mapId) {
  map = L.map('map').setView([0,0], 2);
  // add an OpenStreetMap tile layer
  L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map);

  drawPointFromEditForm();

  map.on('click', setPointFromEvent);

  $('#gtu_lat_long_accuracy').change(drawPointFromEditForm);
  $('#gtu_longitude').change(drawPointFromEditForm);
  $('#gtu_latitude').change(drawPointFromEditForm);

  $('#location .clear_prop').click(function() {
    $(this).closest('tr').find('input').val('');
    drawPointFromEditForm();
  });
}

function drawPointFromEditForm() {
  try{
    var point = new L.LatLng($('#gtu_latitude').val(), $('#gtu_longitude').val());
  }
  catch(e){
    return;
  }
  drawPoint(point, $('#gtu_lat_long_accuracy').val());

  if(accuracy) { //If we have a marker accuracy, zoom on it
    map.fitBounds(accuracy.getBounds());
  }
}

function drawPoint(latlng, acc){
  if(marker) {
    marker.setLatLng(latlng);
  } else {
    marker = L.marker(latlng, {draggable:true}).addTo(map);
    marker.on('dragend', setPointFromEvent);
  }
  if(! accuracy) {
    accuracy = L.circle(marker.getLatLng(), acc).addTo(map);
  }
  else {
    accuracy.setRadius(acc).setLatLng(marker.getLatLng());
  }
}

function setPointFromEvent( e ) {
  var pt ;
  if(e.latlng){ //Click events
    pt = e.latlng
  }else { //Drag end events
    pt = e.target.getLatLng();
  }
  pt = pt.wrap();

  $('#gtu_latitude').val(pt.lat)
  $('#gtu_longitude').val(pt.lng)
  
    //ftheeten 2015 06 02
  $(".convertDD2DMSGeneral").mouseleave();

  drawPoint(pt, $('#gtu_lat_long_accuracy').val());
  fetchElevation(pt);
}

function fetchElevation(lonlat) {
  $.ajax({
    type: "GET",
    dataType: 'JSONP',
    key: 'Fmjtd%7Cluub2durn1%2Cbx%3Do5-9u2x14',
    url: 'https://open.mapquestapi.com/elevation/v1/profile',
    data : {inFormat:'kvp', outFormat: 'json', latLngCollection: lonlat.lat+','+lonlat.lng, unit:'m', shapeFormat: 'raw'},
    success: function(data){
      if(data.elevationProfile[0])
        $('#gtu_elevation').val(data.elevationProfile[0].height);
      else
        $('#gtu_elevation').val(0);
    }
  });
}

//Reverse GeoCoding using OSM nominatim
/*
function fetchPositions(lonlat, zoom) {
  $.ajax({
    url: 'http://nominatim.openstreetmap.org/reverse',
    dataType: 'json',
    data: { lat: lonlat.lat, lon: lonlat.lon, zoom: zoom, addressdetails:1, format: 'json'  },
    success: function(data) {
      container = $('#reverse_tags ul');
      container.html('');
      container.append($('<li></li>').text(data.address.country).attr({ 'data-group': 'administrative area', 'data-subgroup': 'country' }));

      if( data.address.state != undefined )
        container.append($('<li></li>').text(data.address.state).attr({ 'data-group': 'administrative area', 'data-subgroup': 'state' }));

      if( data.address.city != undefined )
        container.append($('<li></li>').text(data.address.city).attr({ 'data-group': 'administrative area', 'data-subgroup': 'city' }));

      container.find('>li').click(onReverseTagClick);
      $('#reverse_tags').show();
    }
  });
}
*/

/*************************** For Search Form *************************/
var results_layer;
var accuracy_layer;
var number_to_fetch = 100;
var mg;
function initSearchMap() {
  $('#show_accuracy').change(function(){
    if(results_layer) {
      if($('#show_accuracy').is(':checked')) {
        map.addLayer(accuracy_layer);
      } else {
        map.removeLayer(accuracy_layer);
      }
    }
  });

  $('#show_as_map').click(function(){
    if($(this).is(':checked')) {
      $('#map_search_form').show();
      map.invalidateSize();
      if($('#gtu_filters_lat_from').val() != '' &&  $('#gtu_filters_lat_to').val() != '' &&
        $('#gtu_filters_lon_from').val() != '' &&   $('#gtu_filters_lat_to').val() != '')
      {
          try{
            map.fitBounds([
              [ $('#gtu_filters_lat_from').val(), $('#gtu_filters_lon_from').val()],
              [ $('#gtu_filters_lat_to').val(),  $('#gtu_filters_lon_to').val()]
            ]);
          }
          catch (e) {
            map.setView([0,0], 2);
          }
      } else {
        map.setView([0,0], 2);
      }
      if($('#gtu_filter').length) {
        //$(this).closest('form').removeClass('search_form');
        $('#gtu_filter').unbind('submit.sform');
        $('#gtu_filter').bind('submit.map_form',map_submit);
        $('.search_results_content').html('');
      }
      $('#lat_long_set table').hide();
    } else {
      if($('#gtu_filter').length) {
        //$(this).closest('form').addClass('search_form');
        $('#gtu_filter').unbind('submit.map_form');
        $('#gtu_filter').bind('submit.sform',$('.catalogue_gtu').data('choose_form').search_form_submit);
      }
      $('#lat_long_set table').show();
      $('#map_search_form').hide();
    }
  });

  $('#lat_long_set .clear_prop').click(function(event) {
    event.preventDefault();
    $(this).closest('tr').find('input').val('');
  });

  map = L.map('smap', {minZoom: 2} ).setView([0,0], 3);
  accuracy_layer = L.layerGroup([]);
  if($('#show_accuracy').is(':checked'))
    accuracy_layer.addTo(map);
  // add an OpenStreetMap tile layer
  L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map);

  //ftheeten 2015 08 03 repalced by Draw rectangle
  //map.on('move', updateLatLong);

  //Custom radius and icon create function
  mg = new L.MarkerClusterGroup({
          maxClusterRadius: 25,
          spiderfyOnMaxZoom: true, showCoverageOnHover: false, zoomToBoundsOnClick: true
  });
  map.addLayer(mg);
  
  
    //ftheeten 2015 08 03
  //button rectangle
  
   var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        var drawControl = new L.Control.Draw({
        position: 'topleft',
        draw: {
            polyline: false,
            polygon: false, 
            rectangle: {repeatMode: false},
            circle: false,
            marker: false,
			remove:true
        },
        edit: false,

    });
        map.addControl(drawControl);

        map.on('draw:created', function (e) {
			
			 drawnItems.clearLayers();
            var type = e.layerType;
                layer = e.layer;
				
				if (type === 'rectangle') {

					arrayBounds=layer.getLatLngs();

					updateLatLongRectangle(L.latLngBounds(arrayBounds));
				}
            drawnItems.addLayer(layer);
        });
		
		//button remove
		L.Control.RemoveAll = L.Control.extend(
		{
			options:
			{
				position: 'topleft',
			},
			onAdd: function (map) {
				var controlDiv = L.DomUtil.create('div', 'leaflet-draw-toolbar leaflet-bar');
				L.DomEvent
					.addListener(controlDiv, 'click', L.DomEvent.stopPropagation)
					.addListener(controlDiv, 'click', L.DomEvent.preventDefault)
				.addListener(controlDiv, 'click', function () {
					drawnItems.clearLayers();
					clearLatLongRectangle();
				});

				var controlUI = L.DomUtil.create('a', 'leaflet-draw-edit-remove', controlDiv);
				controlUI.title = 'Remove All Polygons';
				controlUI.href = '#';
				return controlDiv;
			}
		});
		var removeAllControl = new L.Control.RemoveAll();
		map.addControl(removeAllControl);
}

function updateLatLong() {
  bounds = map.getBounds();
  if($('#show_as_map').is(':checked')) {
    if($('#gtu_filters_lat_from').length) {
      $('#gtu_filters_lat_from').val(bounds.getNorthWest().wrap().lat );
      $('#gtu_filters_lon_from').val(bounds.getNorthWest().wrap().lng);

      $('#gtu_filters_lat_to').val(bounds.getSouthEast().wrap().lat);
      $('#gtu_filters_lon_to').val(bounds.getSouthEast().wrap().lng);
    }else if($('#specimen_search_filters_lat_from').length) {
      $('#specimen_search_filters_lat_from').val(bounds.getNorthWest().wrap().lat );
      $('#specimen_search_filters_lon_from').val(bounds.getNorthWest().lng);

      $('#specimen_search_filters_lat_to').val(bounds.getSouthEast().wrap().lat);
      $('#specimen_search_filters_lon_to').val(bounds.getSouthEast().wrap().lng);

    }

  }
}

//ftheeten 2015 08 03
function updateLatLongRectangle(bounds) {
  
  if($('#show_as_map').is(':checked')) {
    if($('#gtu_filters_lat_from').length) {
      $('#gtu_filters_lat_from').val(bounds.getNorthWest().wrap().lat );
      $('#gtu_filters_lon_from').val(bounds.getNorthWest().wrap().lng);

      $('#gtu_filters_lat_to').val(bounds.getSouthEast().wrap().lat);
      $('#gtu_filters_lon_to').val(bounds.getSouthEast().wrap().lng);
    }else if($('#specimen_search_filters_lat_from').length) {
      $('#specimen_search_filters_lat_from').val(bounds.getNorthWest().wrap().lat );
      $('#specimen_search_filters_lon_from').val(bounds.getNorthWest().lng);

      $('#specimen_search_filters_lat_to').val(bounds.getSouthEast().wrap().lat);
      $('#specimen_search_filters_lon_to').val(bounds.getSouthEast().wrap().lng);

    }

  }
}

//ftheeten 2015 08 03
function clearLatLongRectangle() {
  
  if($('#show_as_map').is(':checked')) {
    
      $('#specimen_search_filters_lat_from').val('');
      $('#specimen_search_filters_lon_from').val('');

      $('#specimen_search_filters_lat_to').val('');
      $('#specimen_search_filters_lon_to').val('');

    }

  
}

function map_submit(event) {
  event.preventDefault();

  $('.paging_info').hide();
  if(results_layer) {
    // Clear previous search
    mg.clearLayers();
    results_layer.clearLayers();
    accuracy_layer.clearLayers();
  }

  //Load results
  $.ajax({
    type: "POST",
    url: $('#gtu_filter').attr('action')+'/format/json?gtu_filters%5Brec_per_page%5D=' + number_to_fetch +'&'+ $('#gtu_filter').serialize(),
    dataType: 'json',
    success: function (response) {
      results_layer = L.geoJson(response, {
        pointToLayer: function (feature, latlng) {
          var fg = L.marker(latlng).bindPopup(feature.properties.content);
          L.circle(latlng, feature.properties.accuracy)
            .on('click', function() {
             fg.openPopup();
            })
            .addTo(accuracy_layer);
          return fg;
        }
      }).addTo(mg);
    }
  });

  //Load Page counts
  $.ajax({
    url: $('#gtu_filter').attr('action')+'/format/text/extd/count?gtu_filters%5Brec_per_page%5D=' + number_to_fetch +'&'+ $('#gtu_filter').serialize(),
    success: function(html) {
      $('.paging_info .inner_text').html(html);
      $('.paging_info').show();
    }
  });
  return false;
}
