var openCycleMapLayer = new ol.layer.Tile({
  source: new ol.source.OSM({
    attributions: [
      'All maps © <a href="https://www.opencyclemap.org/">OpenCycleMap</a>',
      ol.source.OSM.ATTRIBUTION
    ],
    url: 'https://{a-c}.tile.thunderforest.com/cycle/{z}/{x}/{y}.png'
  })
});

var openSeaMapLayer = new ol.layer.Tile({
  source: new ol.source.OSM({
    attributions: [
      'All maps © <a href="http://www.openseamap.org/">OpenSeaMap</a>',
      ol.source.OSM.ATTRIBUTION
    ],
    opaque: false,
    url: 'https://tiles.openseamap.org/seamark/{z}/{x}/{y}.png'
  })
});


var map = new ol.Map({
  layers: [
    openCycleMapLayer,
    openSeaMapLayer
  ],
  target: 'map',
  controls: ol.control.defaults({
    attributionOptions: /** @type {olx.control.AttributionOptions} */ ({
      collapsible: false
    })
  }),
  view: new ol.View({
    maxZoom: 18,
    center: [-244780.24508882355, 5986452.183179816],
    zoom: 15
  })
});
