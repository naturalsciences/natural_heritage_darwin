<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<div class="ui-tooltip-titlebar">
<div id="ui-tooltip-modal-title" class="ui-tooltip-title" aria-atomic="true" >View IIIF Snippet</div>
<a class="ui-state-default ui-tooltip-close ui-tooltip-icon" title="Close tooltip" aria-label="Close tooltip" role="button">
<span class="ui-icon ui-icon-close">×</span>
</a>
</div>


<span>
	   <div id="iiif_map" class="map" style="width: 100%; height:500px; display:inline-block"></div>
	   <select id="iiif_chooser" name="iiif_chooser"></select><br/>
       <a href="<?php print( sfConfig::get('dw_iiif_viewer').$link->getUrl());?>" target="_blank" class='complete_widget'>
        <?php echo __('To IIIF Viewer');?>
      </a>
                  
</span>
  <div  style="text-align:center;"><?php echo __("URL");?>: <a target="_blank" href="<?php echo(str_replace('/embed','', $link->getUrl()));?>" ><?php echo(str_replace('/embed','', $link->getUrl()));?></a></div>


<script>



/*import 'ol/ol.css';
import IIIF from 'ol/source/IIIF';
import IIIFInfo from 'ol/format/IIIFInfo';
import Map from 'ol/Map';
import TileLayer from 'ol/layer/Tile';
import View from 'ol/View';
*/

var img_list=Array();
var layers=Array();
var maps=Array();
var map;
var layer;

var get_img=function(data)
{
	if("images" in data)
	{
		$.each(data.images, function( index, value ) {
			
			img_list.push(value["@id"]);
		});
	}		
}

var seqs=function(data)
{
	if("canvases" in data)
	{
		$.each(data.canvases, function( index, value ) {
			
			get_img(value);
		});
	}		
}

function refreshMap(imageInfoUrl) {
  fetch(imageInfoUrl)
    .then(function (response) {
      response
        .json()
        .then(function (imageInfo) {
          var options = new ol.format.IIIFInfo(imageInfo).getTileSourceOptions();
          if (options === undefined || options.version === undefined) {
            //notifyDiv.textContent =
            //  'Data seems to be no valid IIIF image information.';
			console.log('Data seems to be no valid IIIF image information.');
            return;
          }
          options.zDirection = -1;
          var iiifTileSource = new ol.source.IIIF(options);
          layer.setSource(iiifTileSource);
          map.setView(
            new ol.View({
              resolutions: iiifTileSource.getTileGrid().getResolutions(),
              extent: iiifTileSource.getTileGrid().getExtent(),
              constrainOnlyCenter: true,
            })
          );
          map.getView().fit(iiifTileSource.getTileGrid().getExtent());
		  console.log('initialized');
          //notifyDiv.textContent = '';
        })
        .catch(function (body) {
          console.log('Could not read image info json. ' + body);
        });
    })
    .catch(function () {
       console.log('Could not read data from URL.');
    });
}

var create_ol_iiif=function()
{
	if(img_list.length>0)
	{
		 console.log(img_list);
		 layer = new ol.layer.Tile(),
		 map = new ol.Map({
			layers: [layer],
			target: 'iiif_map'
		  });
		  refreshMap(img_list[0]);
		  for(var i=0;i<img_list.length; i++)
		  {
			  $('#iiif_chooser').append($('<option>', {
					value: img_list[i],
					text: img_list[i]
				}));
		  }
	}
	
}

$('#iiif_chooser').change(
	function()
	{
		refreshMap($('#iiif_chooser').val());
	}
);
		
$(document).ready(

		

        
        function()
        {   
			
			$(".ui-tooltip-close").click(
				function()
				{

						$('body').trigger('close_modal');
				}
			);
			var manifest_url='<?php print($link->getUrl());?>';
			
			
			  $.getJSON( manifest_url, {				
				format: "json"
			  }).done(function( data ) 
				{
					
					if("sequences" in data)
					{
						
						$.each(data.sequences, function( index, value ) {
							seqs(value);
						});
					}
					create_ol_iiif();
				}
				);
				
		}
		);

</script>

