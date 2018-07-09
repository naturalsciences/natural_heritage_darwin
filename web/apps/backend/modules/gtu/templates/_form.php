<!--JMHerpers 2018 07 02 added links for OL-->
<script language="JavaScript" type="text/javascript" src="<?php print(public_path('/openlayers/v4.x.x-dist/ol.js'));?>"></script>
<script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList,URL"></script>
<link rel="stylesheet" href="<?php print(public_path('/openlayers/v4.x.x-dist/ol.css'));?>">
<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<!--[if lte IE 8]>
    <link rel="stylesheet" href="/leaflet/leaflet.ie.css" />
<![endif]-->

<script type="text/javascript">
$(document).ready(function () 
{
  $('body').catalogue({});
});
</script>

<?php echo form_tag('gtu/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()), array('class'=>'edition'));?>

<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th class="top_aligned"><?php echo $form['code']->renderLabel() ?></th>
        <td>
          <?php echo $form['code']->renderError() ?>
          <?php echo $form['code'] ?>
        </td>
		<!--rmca 2016 06 21-->
		<td>
			 <a  class="take_specimen_code button_rmca" >Take specimen code</a>
		</td>
        <td>
			 <a  class="take_gtu_code button_rmca" >Take location code</a>
		</td>
        <td>
			 <a  class="take_ig_code button_rmca" >Take IG code</a>
		</td>
      </tr>
     
    </tbody>
</table>

<?php
$tag_grouped = array();
$avail_groups = TagGroups::getGroups(); 
foreach($form['TagGroups'] as $group)
{
  $type = $group['group_name']->getValue();
  if(!isset($tag_grouped[$type]))
    $tag_grouped[$type] = array();
  $tag_grouped[$type][] = $group;
}
foreach($form['newVal'] as $group)
{
  $type = $group['group_name']->getValue();
  if(!isset($tag_grouped[$type]))
    $tag_grouped[$type] = array();
  $tag_grouped[$type][] = $group;
}
?>
<br/>
<div id="gtu_group_screen">
  <div class="gtu_groups_add">
    <select id="groups_select">
      <option value=""></option>
      <?php foreach(TagGroups::getGroups() as $k => $v):?>
        <option value="<?php echo $k;?>"><?php echo $v;?></option>
      <?php endforeach;?>
    </select>
    <a href="<?php echo url_for('gtu/addGroup'. ($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>" id="add_group"><?php echo __('Add Group');?></a>
  </div>

<!-- ftheeten 2018 03 15 moved down the select list-->  
<div class="tag_parts_screen" alt="<?php echo url_for('gtu/addGroup'. ($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>">
<?php foreach($tag_grouped as  $group_key => $sub_forms):?>
  <fieldset alt="<?php echo $group_key;?>">
    <legend><?php echo __($avail_groups[$group_key]);?></legend>
    <ul>
      <?php foreach($sub_forms as $form_value):?>
	<?php include_partial('taggroups', array('form' => $form_value));?>
      <?php endforeach;?>
    </ul>
    <a class="sub_group"><?php echo __('Add Sub Group');?></a>
  </fieldset>
<?php endforeach;?>
</div>
</div>
    
  <fieldset id="location">
    <legend><?php echo __('Localisation');?></legend>
    <div id="reverse_tags" style="display: none;"><ul></ul><br class="clear" /></div>
	<div>
		<!--DMS/DD selector  ftheeten 2015 05 05-->
		<b><?php echo $form['coordinates_source']->renderLabel() ;?>
		   <?php echo $form['coordinates_source']->renderError() ?>
		</b><br/>
		<?php echo $form['coordinates_source'];?>
	</div>
    <table>
		<tr>
			<td colspan="4">
				<div class="GroupDMS" style="display: None">
					<table >
						<!--DMS columns ftheeten 2015 05 05-->
						<!--<tr >
							<th ><?php echo $form['latitude_dms_degree']->renderLabel() ;?><?php echo $form['latitude_dms_degree']->renderError() ?></th>
							<th ><?php echo $form['latitude_dms_minutes']->renderLabel(); ?><?php echo $form['latitude_dms_minutes']->renderError() ?></th>
							<th ><?php echo $form['latitude_dms_seconds']->renderLabel(); ?><?php echo $form['latitude_dms_seconds']->renderError() ?></th>
							<th ><?php echo $form['latitude_dms_direction']->renderLabel(); ?><?php echo $form['latitude_dms_direction']->renderError() ?></th>
						</tr>-->
						<tr >
							<th ><?php echo 'Latitude';?></th>
							<th />
							<th />
							<th />
						</tr>
						<tr >
							<td ><?php echo 'Degrees: '.$form['latitude_dms_degree'];?></td>
							<td ><?php echo 'Minutes: '.$form['latitude_dms_minutes'];?></td>
							<td ><?php echo 'Seconds: '.$form['latitude_dms_seconds'];?></td>
							<td ><?php echo 'Direction: '.$form['latitude_dms_direction'];?></td>
						</tr>
						<!--<tr >
							<th ><?php echo $form['longitude_dms_degree']->renderLabel() ;?><?php echo $form['longitude_dms_degree']->renderError() ?></th>
							<th ><?php echo $form['longitude_dms_minutes']->renderLabel(); ?><?php echo $form['longitude_dms_minutes']->renderError() ?></th>
							<th class="GroupDMS"><?php echo $form['longitude_dms_seconds']->renderLabel(); ?><?php echo $form['longitude_dms_seconds']->renderError() ?></th>
							<th class="GroupDMS"><?php echo $form['longitude_dms_direction']->renderLabel(); ?><?php echo $form['longitude_dms_direction']->renderError() ?></th>
						</tr>-->
						<tr >
							<th ><?php echo 'Longitude';?></th>
							<th />
							<th />
							<th />
						</tr>
						<tr >
							<td><?php echo 'Degrees: '.$form['longitude_dms_degree'];?></td>
							<td><?php echo 'Minutes: '.$form['longitude_dms_minutes'];?></td>
							<td><?php echo 'Seconds: '.$form['longitude_dms_seconds'];?></td>
							<td><?php echo 'Direction: '.$form['longitude_dms_direction'];?></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<div class="GroupUTM" style="display: None">
					<table>
						<!--DMS columns ftheeten 2015 05 05-->
						<tr>
							<th><?php echo $form['latitude_utm']->renderLabel() ;?><?php echo $form['latitude_utm']->renderError() ?></th>
							<th><?php echo $form['longitude_utm']->renderLabel(); ?><?php echo $form['longitude_utm']->renderError() ?></th>
							<th><?php echo $form['utm_zone']->renderLabel(); ?><?php echo $form['utm_zone']->renderError() ?></th>
						</tr>
						<tr>
							<td><?php echo $form['latitude_utm'];?></td>
							<td><?php echo $form['longitude_utm'];?></td>
							<td><?php echo $form['utm_zone'];?></td>
							
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<th class="GroupDD" style="display: None"><?php echo $form['latitude']->renderLabel() ;?><?php echo $form['latitude']->renderError() ?></th>
			<th class="GroupDD" style="display: None"><?php echo $form['longitude']->renderLabel(); ?><?php echo $form['longitude']->renderError() ?></th>
			<th><?php echo $form['lat_long_accuracy']->renderLabel() ;?><?php echo $form['lat_long_accuracy']->renderError() ?></th>
			<th></th>
		</tr>
		<tr>
			<td class="GroupDD" style="display: None"><?php echo $form['latitude'];?></td>
			<td class="GroupDD" style="display: None"><?php echo $form['longitude'];?></td>
			<td><?php echo $form['lat_long_accuracy'];?></td>
			<td><strong><?php echo __('m');?></strong><!-- <?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?>--></td>
			<td></td>
		</tr>
		<!--JMHerpers 2018 07 02 added openlayers map-->
		<tr>
			<td colspan="3" id="ol_map">
				<style >
					p.collapse{
						display:none;
					}
				</style>
				<form class="form-inline">
				  <label>Or geometry type : &nbsp;</label>
				  <select id="type">
					<option value="None">Drag map</option>
					<option value="LineString">LineString</option>
					<option value="Polygon">Polygon</option>
					<!--<option value="Circle">Circle</option>-->
				  </select>
				  <label class="points_for_geometry"></label>
				</form>
				<div id="list_of_coord">
				</div>
				<div id="wkt">
					<label><?php echo $form['wkt_str']->renderLabel(); ?>&nbsp;: &nbsp;</label>
					<?php echo $form['wkt_str'];?>
				</div>
			</td>
		</tr>
		
	    <tr>
			<th><?php echo $form['elevation']->renderLabel(); ?><?php echo $form['elevation']->renderError() ?></th>
			<th><!--<?php echo $form['elevation_unit']->renderLabel(); ?>--><?php echo $form['elevation_unit']->renderError() ?></th>
			<th><?php echo $form['elevation_accuracy']->renderLabel() ;?><?php echo $form['elevation_accuracy']->renderError() ?></th>
			<th></th>
		</tr>
		<tr>
			<td><?php echo $form['elevation'];?></td>
			<td><?php echo $form['elevation_unit'];?></td>
			<td><?php echo $form['elevation_accuracy'];?></td>
			<td></td>
		</tr>
		<!--<tr>
			<td colspan="3"><div style="width:100%; height:400px;" id="map"></div></td>
			<td>
				<script type="text/javascript">
					$(document).ready(function () {
					  initEditMap("map");
					  <?php if($form->getObject()->getLongitude() != ''):?>
						map.setView([<?php echo $form->getObject()->getLatitude();?>,<?php echo $form->getObject()->getLongitude();?>], 12);
					  <?php else:?>
						map.setView([0,0], 2);
					  <?php endif;?>
					});
				</script>
			</td>
		</tr>-->
	
		<!--JMHerpers 2018 07 02 added openlayers map-->
		<tr>
			<td colspan="3" id="ol_map">
				<style >
					p.collapse{
						display:none;
					}
				</style>
				<div id="map_container_nh" class="map_container_nh">
					<div  style="width:500px;height:400px;" id="map2" class="map2"></div>
					<div id="mouse-position"></div>
				</div>
			</td>
		</tr>
    </table>
  </fieldset>

  <table>
    <tfoot>
      <tr>
        <td>
          <?php echo $form->renderHiddenFields(true) ?>

          <?php if (!$form->getObject()->isNew()): ?>
				<?php echo link_to(__('New Gtu'), 'gtu/new') ?>
				&nbsp;<?php echo link_to(__('Duplicate Gtu'), 'gtu/new?duplicate_id='.$form->getObject()->getId()) ?>
          <?php endif?>

          &nbsp;<a href="<?php echo url_for('gtu/index') ?>"><?php echo __('Cancel');?></a>
          <?php if (!$form->getObject()->isNew()): ?>
				&nbsp;<?php echo link_to(__('Delete'), 'gtu/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>


<script  type="text/javascript">

	//ftheeten 2016 02 05
	var showDMSCoordinates;
	var coordViewMode=true;
	//JMHerpers 2018 07 02
	var count=0;
	var controls;
	var wktfeaturegeom;
	var wktfromdata;
	var nbrpoints;
	var valueType;
	
	//ftheeten 2018 03 15
	<?php if($form->getObject()->isNew()): ?>
	var boolAdministrativeArea2=false;
	var boolArea3=false;
	<?php endif; ?>
	//ftheeten 2016 02 05
	$( "form" ).submit(function( event ) {
	  window.opener.$("#gtu_filters_code").val($("#gtu_code").val());
	 });

	var map;
	var map2;
	var bingBackground;
	var view;
	var mousePositionControl;
	function showOL(lati, longi,zoom)
	   {
		if(count>0){
			$('#map2').empty();
			$('#mouse-position').empty();
		}
		if( lati != '' && longi!= '')  {
			var scaleLineControl;
			mousePositionControl= new ol.control.MousePosition({
				 coordinateFormat: ol.coordinate.createStringXY(4),
				projection:"EPSG:4326",
				className: "custom-mouse-position",
				target: document.getElementById("mouse-position"),
				undefinedHTML: "&nbsp;"
			});
			scaleLineControl = new ol.control.ScaleLine();
			bingBackground= new ol.layer.Tile({
				preload: Infinity,
				source: new ol.source.BingMaps({key:"Ap9VNKmWsntrnteCapydhid0fZxzbV_9pBTjok2rQZS4pi15zfBbIkJkvrZSuVnJ",  imagerySet:"AerialWithLabels" })
			});
			view= new ol.View({
				center: [-4,15],
				zoom: zoom
			});
					
			var geometry=new ol.geom.Point([parseFloat(longi),parseFloat(lati)]);
			var style= new ol.style.Style({
				image: new ol.style.Circle({
					radius: 10,
					stroke: new ol.style.Stroke({
						color: "#fff"}),
					fill: new ol.style.Fill({
						color: "#3399CC"})
				}),
				text: new ol.style.Text({
					text: "x",
					fill: new ol.style.Fill({
						color: "#fff"
					})
				})
			});
				   
			 var iconFeature = new ol.Feature({
				 label:"x",
				geometry: geometry.transform("EPSG:4326", "EPSG:3857")
			});
						
			var vectorSource = new ol.source.Vector({
				features: [iconFeature]
			});

			var vectorLayer = new ol.layer.Vector({
				source: vectorSource,
				style: style
			});

			map=new ol.Map({
				layers:[bingBackground, vectorLayer],
				target: "map2",
				view: view,
				controls: ol.control.defaults({
					attributionOptions: ({collapsible: false})
				}).extend([mousePositionControl, scaleLineControl ])
			});
						 

				
			var extent = vectorLayer.getSource().getExtent();
			map.getView().fit(extent);
			map.getView().setZoom(11);
			
			count++;
		}
		
		////////////////map with polygon
		const typeSelect =  document.getElementById('type');

		typeSelect.onchange = function() {
			$('#map2').empty();
			$('#mouse-position').empty();
			
			//create new fields to enter coordinates
			switch (typeSelect.value)
			{
				case "LineString": 
				case "Polygon": 
				   $('.points_for_geometry').html('<form class="form_points">of <input class="nbrpoints" type="integer" name="points" maxlength="2" size="3"> points</form>');
				   break;
			}
			
			//Create new map with same background 
			var source = new ol.source.Vector({wrapX: false});

			var styleLine= new ol.style.Style({
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
			})
			
			var vector = new ol.layer.Vector({
				source: source,
				style:styleLine
			});

			map2 = new ol.Map({
				target: 'map2',
				layers: [
				  bingBackground,vector
				],
				view: new ol.View({
				  center: ol.proj.fromLonLat([parseFloat(longi),parseFloat(lati)]),
				  zoom: 11
				})
			});
						
			var draw;
			function addInteraction() {
				valueType = typeSelect.value;
				if (valueType !== 'None') {
				  draw = new ol.interaction.Draw({
					source: source,
					type: valueType
				  });
				  map2.addInteraction(draw);
				  
				draw.on('drawend', function (event) {
					var format = new ol.format.WKT();
					wktfeaturegeom = format.writeGeometry(event.feature.getGeometry());
					$('.wkt').val(wktfeaturegeom);
					
				});
				}
			}

			map2.removeInteraction(draw);
			addInteraction();
			
			$('.nbrpoints').focusout(function() {
				draw_points_input_fields();
			});
		};
	}
	
	//JMHerpers 2018 07 09
	function draw_points_input_fields(){
		if ($('.nbrpoints').val() != ""){
					nbrpoints = $('.nbrpoints').val();
					var htmlcontent = '<form class="list_points"><table><tr><td colspan="7">Latitude:</td><td colspan="7">Longitude:</td></tr>';
					for (i=0;i<nbrpoints;i++){
						htmlcontent = htmlcontent + '<tr>';
						htmlcontent = htmlcontent + '<td><input class="lati_deg_'+i+' vsmall_size" type="text" maxlength = "3"></td>';
						htmlcontent = htmlcontent + '<td>deg.</td>';
						htmlcontent = htmlcontent + '<td><input class="lati_min_'+i+' vsmall_size" type="text" maxlength="2" ></td>';
						htmlcontent = htmlcontent + '<td>min.</td>';
						htmlcontent = htmlcontent + '<td><input class="lati_sec_'+i+' vsmall_size" type="text" maxlength="8"></td>';
						htmlcontent = htmlcontent + '<td>sec.</td>';
						htmlcontent = htmlcontent + '<td style="border-right: 1px solid #000000;"  ><select class="latns_'+i+'">';
						htmlcontent = htmlcontent + '		<option value="N">N</option>';
						htmlcontent = htmlcontent + '		<option value="S">S</option>';
						htmlcontent = htmlcontent + '	</select></td>';
						htmlcontent = htmlcontent + '<td>&nbsp;<input class="longi_deg_'+i+' vsmall_size" type="text" maxlength="2"></td>';
						htmlcontent = htmlcontent + '<td>deg.</td>';
						htmlcontent = htmlcontent + '<td><input class="longi_min_'+i+' vsmall_size" type="text" maxlength="2"></td>';
						htmlcontent = htmlcontent + '<td>min.</td>';
						htmlcontent = htmlcontent + '<td><input class="longi_sec_'+i+' vsmall_size" type="text" maxlength="8"></td>';
						htmlcontent = htmlcontent + '<td>sec.</td>';
						htmlcontent = htmlcontent + '<td><select class="longew_'+i+'">';
						htmlcontent = htmlcontent + '		<option value="E">E</option>';
						htmlcontent = htmlcontent + '		<option value="W">W</option>';
						htmlcontent = htmlcontent + '	</select></td>';
						htmlcontent = htmlcontent + '</tr>';
					};
					htmlcontent = htmlcontent + '</table><BR><input onclick="drawarea()" class="btn_drawlines" type="button" value="Draw on map"></form>';
					$('#list_of_coord').html(htmlcontent);
				}
	}
	
	//JMHerpers 2018 07 06
	function drawarea(){
		var errorval = false;
		for (i=0;i<nbrpoints;i++){
			if(((!$.isNumeric($('.lati_deg_'+i).val()) | !$.isNumeric($('.lati_min_'+i).val()) | !$.isNumeric($('.lati_sec_'+i).val()) | 
			   !$.isNumeric($('.longi_deg_'+i).val()) | !$.isNumeric($('.longi_min_'+i).val()) | !$.isNumeric($('.longi_sec_'+i).val()) ) &
			   ( $('.lati_deg_'+i).val().length + $('.lati_min_'+i).val().length + $('.lati_sec_'+i).val().length + 
			     $('.longi_deg_'+i).val().length + $('.longi_min_'+i).val().length + $('.longi_sec_'+i).val().length != 0
			   ))|
				$('.lati_deg_'+i).val() < 0 | $('.lati_deg_'+i).val() > 90 | $('.longi_deg_'+i).val() < 0 | $('.longi_deg_'+i).val() > 180 |
			    $('.lati_min_'+i).val() < 0 | $('.lati_min_'+i).val() > 59 | $('.longi_min_'+i).val() < 0 | $('.longi_min_'+i).val() > 59 |
			    $('.lati_sec_'+i).val() < 0 | $('.lati_sec_'+i).val() > 59 | $('.longi_sec_'+i).val() < 0 | $('.longi_sec_'+i).val() > 59 
			){
				errorval = true;
			}
		}
		if(errorval == true){
				alert('There are errors in the coordinates entered!');
		}else{
			var latSign = 1;
			var longSign = 1;
			var latDeci= 0;
			var longDeci= 0;
			
			//////calculate wkt
			wktfromdata = valueType.toUpperCase() +"(";
			var latSign0 = 1;
			var longSign0 = 1;
			for (i=0;i<nbrpoints;i++){
				if($( '.latns_'+i+' option:selected' ).text() == "S"){
					latSign = -1;
				}
				if($( '.longew_'+i+' option:selected' ).text() == "W"){
					longSign = -1;
				}
				if(i==0){
					latSign0 = latSign;
					longSign0 = longSign;
				}
				
				latDeci= latSign * (parseFloat($('.lati_deg_'+i).val()) + ( parseFloat($('.lati_min_'+i).val())/60) + ( parseFloat($('.lati_sec_'+i).val())/3600));
				longDeci= longSign  * (parseFloat($('.longi_deg_'+i).val()) + ( parseFloat($('.longi_min_'+i).val())/60) + ( parseFloat($('.longi_sec_'+i).val())/3600));
				wktfromdata = wktfromdata + longDeci + " " + latDeci + ",";
			}
			
			wktfromdata = wktfromdata.substring(0, wktfromdata.length-1) + ")";
		//	alert(wktfromdata);
			$(".wkt").val(wktfromdata);

			//////show wkt on map
			var format = new ol.format.WKT();

			var featureWKT = format.readFeature(wktfromdata, {
				dataProjection: 'EPSG:4326',
				featureProjection: 'EPSG:3857'
			});
			
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
			})

			var vectorWKT = new ol.layer.Vector({
				source: new ol.source.Vector({
				  features: [featureWKT]
				}),
				style: styleWKT
			});
			
			$('#map2').empty();
			$('#mouse-position').empty();
		//	alert("lo="+longSign0+" lat="+latSign0);

			map2 = new ol.Map({
				target: 'map2',
				layers: [
				  bingBackground,vectorWKT
				],
				view: new ol.View({
				  center: ol.proj.fromLonLat([parseFloat(longSign0 * $('.longi_deg_0').val() ),parseFloat(latSign0 * $('.lati_deg_0').val())]),
				  zoom: 9
				})
			});
		}
	};
	
	function fill_points_lines(){
		var wktval = $('.wkt').val();
		var wktval2 = wktval.substring(wktval.indexOf("(")+1, wktval.length-1);
		var arraypoints = wktval2.split(","); 
		
		$('.points_for_geometry').html('<form class="form_points">of <input class="nbrpoints" type="integer" name="points" maxlength="2" size="3"> points</form>');
		$('.nbrpoints').val(arraypoints.length);
		draw_points_input_fields();
		valueType = wktval.substring(0,wktval.indexOf("("));
		
		for (i=0;i<arraypoints.length;i++){
			var lat = arraypoints[i].substring(arraypoints[i].indexOf(" ")+1,arraypoints[i].length);
			var lng = arraypoints[i].substring(0,arraypoints[i].indexOf(" "));
			$(".latns_"+i +" option[value='N']").attr("selected", true);
			//$('.latns_'+i).val("N");
			alert("lat="+lat+" long="+lng);
			if(lat < 0){
				$(".latns_"+i +" option[value='S']").attr("selected", true);
				//$('.latns_'+i).val("S");
				alert("lat="+lat+" long="+lng +" val="+$('.latns_'+i).val());
			}
			//$('.longew_'+i).val("E");
			$(".longew_"+i +" option[value='E']").attr("selected", true);
			if(lng < 0){
				$(".longew_"+i +" option[value='W']").attr("selected", true);
				//$('.longew_'+i).val("W");
			}
			$('.longi_deg_'+i).val(Math.floor(Math.abs(lng)));
			$('.lati_deg_'+i).val(Math.floor(Math.abs(lat)));
			var decimalLongitude=Math.abs(lng)-Math.floor(Math.abs(lng));
			var decimalLongitudeResultMinute=Math.floor(decimalLongitude*60);
			$('.longi_min_'+i).val(decimalLongitudeResultMinute);
			var decimalLatitude=Math.abs(lat)-Math.floor(Math.abs(lat));
			var decimalLatitudeResultMinute=Math.floor(decimalLatitude*60);
			$('.lati_min_'+i).val(decimalLatitudeResultMinute);
			var decimalsLongitudeForSeconds=Math.abs(lng)-Math.floor(Math.abs(lng))-(decimalLongitudeResultMinute/60);
			$('.longi_sec_'+i).val(decimalsLongitudeForSeconds*3600);
			var decimalsLatitudeForSeconds=Math.abs(lat)-Math.floor(Math.abs(lat))-(decimalLatitudeResultMinute/60);
			$('.lati_sec_'+i).val(decimalsLatitudeForSeconds*3600);
		}
	};
	
	////////////////document ready
	$(document).ready(function () {
		
		//JMHerpers 2018 07 02
		//if(<?php echo $form->getObject()->getLatitude();?> != ""){
		if($('#gtu_latitude').val().length != 0){
			//$lati = <?php echo $form->getObject()->getLatitude();?>;
			//$longi = <?php echo $form->getObject()->getLongitude();?>;
			$lati = <?php print($form->getObject()->getLatitude()?: '-4');?>;
			$longi = <?php print($form->getObject()->getLongitude() ?:'13');?>;
			showOL($lati,$longi,1);
		}else{
			if($('.wkt').val().length != 0){
				/*$lati = "0";
				$longi = "0";
				showOL($lati,$longi,2);*/
				fill_points_lines();
				drawarea();
			}else{
				$lati = "-4";
				$longi = "15";
				showOL($lati,$longi,1);
			}
		}
		
		//ftheeten 2016 02 05

		showDMSCoordinates=false;

		$('.tag_parts_screen .clear_prop').live('click', function()
		{
		  parent_el = $(this).closest('li');
		  $(parent_el).find('input').val('');
		  $(parent_el).hide();

		  sub_groups  = parent_el.parent();
		  if(sub_groups.find("li:visible").length == 0)
		  {
			  sub_groups.closest('fieldset').hide();
			disableUsedGroups();
		  }
		});

		disableUsedGroups();
		$('.purposed_tags li').live('click', function()
		{

		  input_el = $(this).parent().closest('li').find('input[id$="_tag_value"]');
		  /*if(input_el.val().match("\;\s*$"))
		  {
			input_el.val( input_el.val() + $(this).text() );
		  }
		  else
		  {
			input_el.val( input_el.val() + " ; " +$(this).text() );
		  }*/
		  //ftheeten 2016 03 11
		  input_el.val( $(this).text() );
		  input_el.trigger('click');
		});

		$('input[id$="_tag_value"]').live('keydown click',purposeTags);

	   function purposeTags(event)
	   {
		  if (event.type == 'keydown')
		  {
			var code = (event.keyCode ? event.keyCode : event.which);
			if (code != 59 /* ;*/ && code != $.ui.keyCode.SPACE ) return;
		  }
		  parent_el = $(this).closest('li');
		  group_name = parent_el.find('input[name$="\[group_name\]"]').val();
		  sub_group_name = parent_el.find('[name$="\[sub_group_name\]"]').val();
		  if(sub_group_name == '' || $(this).val() == '') return;
		  $('.purposed_tags').hide();
		  $.ajax({
			type: "GET",
			url: "<?php echo url_for('gtu/purposeTag');?>" + '/group_name/' + group_name + '/sub_group_name/' + sub_group_name + '/value/'+ $(this).val(),
			success: function(html)
			{
			  parent_el.find('.purposed_tags').html(html);
			  parent_el.find('.purposed_tags').show();
			}
		  });
		}

		$('#add_group').click(function(event)
		{
		   event.preventDefault();
		  selected_group = $('#groups_select option:selected').val();
		  addGroup(selected_group);
		});

		$('a.sub_group').live('click',function(event)
		{
		
		  event.preventDefault();
		  addSubGroup( $(this).closest('fieldset').attr('alt'));
		});
		
		
		//ftheeten 2016 09 15
		checkCoordSourceState();
		<?php if($form->getObject()->isNew()): ?>
		//ftheeten 2018 03 15
		var initAdminstrativeGroupsOnLoad=function()
		{
			addGroup("administrative area");			

		}
		initAdminstrativeGroupsOnLoad();
		<?php endif; ?>
		

	});

	function addSubGroup(selected_group, default_type, value)
	{
		hideForRefresh('#gtu_group_screen');
		fieldset = $('fieldset[alt="'+selected_group+'"]');
		if( fieldset.length ==0 )
		{
		  addGroup(selected_group, default_type, value);
		}
		list =  fieldset.find('>ul');
		$.ajax({
		  type: "GET",
		  url: $('.tag_parts_screen').attr('alt')+'/group/'+ selected_group + '/num/' + (0+$('.tag_parts_screen ul li').length),
		  success: function(html)
		  {
			html = $(html);
			html.find('.complete_widget select').val(default_type);

			if(value != undefined && value !='')
			{
			  html.find('.tag_encod input').val(value);
			}
			list.append(html);

			showAfterRefresh('#gtu_group_screen');
		  }
    });
}

function addTagToGroup(group, sub_group, tag)
{
    console.log('brol '+group + '-'+sub_group);

  if($('fieldset[alt="'+group+'"] .complete_widget input, fieldset[alt="'+group+'"] .complete_widget option:selected').filter(function()
    { return $(this).is(':visible') && $(this).val() == sub_group; }).length == 0)
  {
    addSubGroup(group, sub_group, tag);
  }
  else
  {
    el = $('fieldset[alt="'+group+'"] .complete_widget input, fieldset[alt="'+group+'"] .complete_widget option:selected').filter('[value="'+sub_group+'"]');
    el_input = el.closest('li').find('.tag_encod input');
    el_input.val( el_input.val()  +' ; ' + tag);
  }
}

function disableUsedGroups()
{
  $('#groups_select option').removeAttr('disabled');
  $('.tag_parts_screen fieldset:visible').each(function()
  {
    var cur_group = $(this).attr('alt');
    $("#groups_select option[value='"+cur_group+"']").attr('disabled','disabled');
    if($("#groups_select option[value='"+cur_group+"']:selected"))
      $('#groups_select').val("");
  });
}

function addGroup(g_val, sub_group, value)
{
  if(g_val != '')
  {
    hideForRefresh('#gtu_group_screen');
    g_name = $('[value="'+g_val+'"]').text();
    $.ajax({
      type: "GET",
      url: $('.tag_parts_screen').attr('alt')+'/group/'+ g_val + '/num/' + (0+$('.tag_parts_screen ul li').length),
      success: function(html)
      {
        html = $(html);
        if( $('fieldset[alt="'+g_val+'"]').length == 0)
        {
          fld = '<fieldset alt="'+ g_val +'"><legend>' + g_name + '</legend><ul></ul><a class="sub_group"><?php echo __('Add Sub Group');?></a></fieldset>';
          $('.tag_parts_screen').append(fld);    
        }
        //@TODO: What if not in select?
        html.find('select').val(sub_group);
        fld_set = $('fieldset[alt="'+g_val+'"]');

        if(value != undefined && value !='')
        {
           html.find(' .tag_encod input').val(value);
        }

        fld_set.find('> ul').append(html);
        fld_set.show();

        disableUsedGroups();
        showAfterRefresh('#gtu_group_screen');
      }
    });
  }
}



//ftheeten 2015 06 02
/*$(".butShowDMS").change(

	function()
	{
		var selected=$( ".butShowDMS" ).val();
		
		var showDMS='display: table-cell';
		var showDD='display: None';
		var showUTM='display: None';

		if(selected=="DD")
		{
			showDMS='display: None';
			showDD='display: table-cell';
			showUTM='display: None';
			
		}
		else if(selected=="DMS")
		{
			showDMS='display: table-cell';
			showDD='display: None';
			showUTM='display: None';
		}
		else if(selected=="UTM")
		{
			showDMS='display: None';
			showDD='display: None';
			showUTM='display: table-cell';
			
		}
		$('.GroupDMS').attr('style',showDMS );
		$('.GroupDD').attr('style', showDD);
		$('.GroupUTM').attr('style', showUTM);
		
		
		//$('.coordinates_source option[value='+selected+']').attr('selected','selected');
	}

);*/


//ftheeten 2016 09 05
function checkCoordSourceState()
{
    var selected=$( ".coordinates_source" ).val();
		
		var showDMS='display: table-cell';
		var showDD='display: None';
		var showUTM='display: None';
		/*var showDMS='display: table-cell';
		var showDD='display: table-cell';
		var showUTM='display: table-cell';*/
		if(selected=="DD")
		{
            
			showDMS='display: None';
			showDD='display: table-cell';
			showUTM='display: None';
			
		}
		else if(selected=="DMS")
		{
                   
			showDMS='display: table-cell';
			showDD='display: None';
			showUTM='display: None';
		}
		else if(selected=="UTM")
		{
                   
			showDMS='display: None';
			showDD='display: None';
			showUTM='display: table-cell';
			
		}
		$('.GroupDMS').attr('style',showDMS );
		$('.GroupDD').attr('style', showDD);
		$('.GroupUTM').attr('style', showUTM);
		
}

//ftheeten 2015 06 02
$(".coordinates_source").change(

	function()
	{
		
		checkCoordSourceState();
		//$('.butShowDMS option[value='+selected+']').attr('selected','selected');
	}

);



function convertCoordinatesDMS2DD()
{

	//if(coordViewMode==false)
	//{
   
		var latD=0.0;
			var latM=0.0;
			var latS=0.0;
			var latSign=1;
			if($(".DMSLatDeg").val().length > 0)
			{
				latD=$(".DMSLatDeg").val().replace(/\,/, ".");
			}
			if($(".DMSLatMin").val().length > 0)
			{
				latM=$(".DMSLatMin").val().replace(/\,/, ".");
			}
			if($(".DMSLatSec").val().length > 0)
			{
				latS=$(".DMSLatSec").val().replace(/\,/, ".");
			}
			if($(".DMSLatSign").val().length > 0)
			{
				latSign=$(".DMSLatSign").val();
			}
			var latDeci= latSign *(parseFloat(latD) + ( parseFloat(latM)/60) + ( parseFloat(latS)/3600));
			if($.isNumeric(latDeci)==false)
			{
				alert('values for DMS coordinates doesn\'t seem numeric, please check your input');
			}
			$(".convertDMS2DDLat").val(latDeci);
			
			
			var longD=0.0;
			var longM=0.0;
			var longS=0.0;
			var longSign=1;
			if($(".DMSLongDeg").val().length > 0)
			{
				longD=$(".DMSLongDeg").val().replace(/\,/, ".");
			}
			if($(".DMSLongMin").val().length > 0)
			{
				longM=$(".DMSLongMin").val().replace(/\,/, ".");
			}
			if($(".DMSLongSec").val().length > 0)
			{
				longS=$(".DMSLongSec").val().replace(/\,/, ".");
			}
			if($(".DMSLongSign").val().length > 0)
			{
				longSign=$(".DMSLongSign").val();
			}
			var longDeci= longSign *(parseFloat(longD) + ( parseFloat(longM)/60) + ( parseFloat(longS)/3600));
			if($.isNumeric(longDeci)==false)
			{
				alert('values for DMS coordinates doesn\'t seem numeric, please check your input');
			}
			else
			{
				$(".convertDMS2DDLong").val(longDeci);
			}
            update_point_on_map($(".convertDMS2DDLat").val(),$(".convertDMS2DDLong").val(), null);
			
		//}
}

function convertCoordinatesDD2DMS()
{
	//if(coordViewMode==false)
	//{
		//ftheeten 2015 05 25
	  //longitude
	  var lat=$(".convertDMS2DDLat").val();
	  var lng=$(".convertDMS2DDLong").val();
	  $(".DMSLongDeg").val(Math.floor(Math.abs(lng)));
	  $(".DMSLongSign option").filter(function()
			{
				if(lng<0.0)
				{
					return $(this).val()<0;
				}
				else
				{
					return $(this).val()>0;
				}
			}).attr('selected',true);
	  var decimalLongitude=Math.abs(lng)-Math.floor(Math.abs(lng));
	  decimalLongitudeResultMinute=Math.floor(decimalLongitude*60);
	   $(".DMSLongMin").val(decimalLongitudeResultMinute);
	  decimalsLongitudeForSeconds=Math.abs(lng)-Math.floor(Math.abs(lng))-(decimalLongitudeResultMinute/60);
	  $(".DMSLongSec").val(decimalsLongitudeForSeconds*3600);
	  
	  //latitude
		$(".DMSLatDeg").val(Math.floor(Math.abs(lat)));
	  $(".DMSLatSign option").filter(function()
			{
				if(lat<0.0)
				{
					return $(this).val()<0;
				}
				else
				{
					return $(this).val()>0;
				}
			}).attr('selected',true);
	  var decimalLatitude=Math.abs(lat)-Math.floor(Math.abs(lat));
	  decimalLatitudeResultMinute=Math.floor(decimalLatitude*60);
	   $(".DMSLatMin").val(decimalLatitudeResultMinute);
	  decimalsLatitudeForSeconds=Math.abs(lat)-Math.floor(Math.abs(lat))-(decimalLatitudeResultMinute/60);
	  $(".DMSLatSec").val(decimalsLatitudeForSeconds*3600);
        update_point_on_map($(".convertDMS2DDLat").val(),$(".convertDMS2DDLong").val(), null);
    //}
}

$(".convertDMS2DDGeneralOnLeave").mouseleave(
	function(event)
	{
		var idControl=event.target.id;
		var value=$("#"+idControl).val();
		if(value.trim().length>0)
		{
				convertCoordinatesDMS2DD();
				//changeCoordinateSource(0);
		}
	}
);

$(".convertDMS2DDGeneralOnLeave").change(
	function(event)
	{
		coordViewMode=false;
		//changeCoordinateSource(0);

	}
);


$(".convertDD2DMSGeneral").change(
	function(event)
	{

		coordViewMode=false;

		convertCoordinatesDD2DMS();
		//changeCoordinateSource(1);

	}
);


$(".convertDMS2DDGeneralOnChange").change(
	function(event)
	{
		coordViewMode=false;

		convertCoordinatesDMS2DD();
		//changeCoordinateSource(0);
		
	}
);


$(".convertDD2DMSGeneral").mouseleave(
	function(event)
	{
		//alert("DD leave");
		var idControl=event.target.id;
		var value=$("#"+idControl).val();
		if(value.trim().length>0)
		{

				convertCoordinatesDD2DMS();
				//changeCoordinateSource(1);
			
		}
	}
);

//ftheeeten 20150610
//to prevent accidental updates of coordibates on mouseleave (as the  GTU are always displayed in "edit" mode)
function detectBothValCoordExisting()
{
	var booleanAlreadyExisting=false;
	var latDD=$(".convertDMS2DDLat").val().trim();
	var latDMS=$(".DMSLatDeg").val().trim();
	var longDD=$(".convertDMS2DDLong").val().trim();
	var longDMS=$(".DMSLongDeg").val().trim();
	if((latDD.length>0||latDMS.length>0)&&(longDD.length>0||longDMS.length>0))
	{
		booleanAlreadyExisting=true;
	}
	return booleanAlreadyExisting;
}

/*function changeCoordinateSource(value)
{

		$(".coordinates_source option").filter(function()
			{
				return value;
			}
		 ).attr('selected',false);
		  $(".coordinates_source option").filter(function()
			{
				return 1-value;
			}
		 ).attr('selected',true);
}
*/
//ftheeten 2016 02 05
//UTM
$(".UTM2DDGeneralOnLeave").change(
	function(event)
	{
		convertUTM();

	}
);

function convertUTM()
{
		zone=$(".UTMZone").val();
		var zoneUTM =  initUTM('tmp', zone.replace( /\D+/g, ''), zone.replace( /[0-9]*/g, ''));

		var wgs84=proj4('EPSG:4326');
		var lat=$(".UTMLat").val();
	    var lng=$(".UTMLong").val();
		var conv=proj4(zoneUTM,wgs84,[lng,lat]);

		$(".convertDMS2DDLat").val(conv[1].toFixed(4));
		$(".convertDMS2DDLong").val(conv[0].toFixed(4));
	update_point_on_map($(".convertDMS2DDLat").val(),$(".convertDMS2DDLong").val(), null);
		
}

function initUTM(name, zone, direction )
{
	
	var dir="";
	if(direction=="S")
	{
		dir="+ south";
	}
	var strProj='+proj=utm +zone='+zone+' '+dir+' +datum=WGS84 +units=m +no_defs ';

	return strProj;
}

        //rmca 2016 06 21--
        $(".take_specimen_code").click(
		function()
		{
        
            var code_word="";
            if(window.opener.$("#specimen_newCodes_0_code_prefix").length>0)
            {
                
                code_word="#specimen_newCodes_0_code";
            }
            else if(window.opener.$("#specimen_Codes_0_code_prefix").length>0)
            {
                    code_word="#specimen_Codes_0_code";
            }
			
			var valSpecCodePrefix= window.opener.$(code_word+"_prefix").val()||'';
			var valSpecCodePrefixSeparator= window.opener.$(code_word+"_prefix_separator").val()||'';
			var valSpecCode= window.opener.$(code_word).val()||'';
			var valSpecCodeSuffixSeparator= window.opener.$(code_word+"_suffix_separator").val()||'';
			var valSpecCodeSuffix= window.opener.$(code_word+"_suffix").val()||'';
			var codeTotal=valSpecCodePrefix.concat(valSpecCodePrefixSeparator.concat(valSpecCode.concat(valSpecCodeSuffixSeparator.concat(valSpecCodeSuffix))));
			 if(!!codeTotal)
            {
                    $("#gtu_code").val(codeTotal);
            }
            else
            {
                 $("#gtu_code").val('');
            }
		});
        
         //ftheeten 2016 06 28
        $(".take_gtu_code").click( function()
        {
           var idGTU=window.opener.$(".view_loc_code").text();
           
            if(!!idGTU)
            {
                    $("#gtu_code").val(idGTU);
            }
            else
            { 
                 $("#gtu_code").val('');
            }
        });
        
         //ftheeten 2016 06 28
        $(".take_ig_code").click( function()
        {
           var idGTU=window.opener.$("#specimen_ig_ref_name").val();
            if(!!idGTU)
            {
                    $("#gtu_code").val(idGTU);
            }
            else
            {
                 $("#gtu_code").val('');
            }
        });
        
        //ftheteen 2016 09 15
        function update_point_on_map( lati, longi, accu)
        {
			//JMHerpers 2018 07 02
			showOL(lati,longi,1);
			
            //var latlng = L.latLng(lati, longi);
            //drawPoint(latlng, accu );

        }
		
		//ftheeten 2018 03 15 to add country, municipality and exact sites widgets
		$(document).ajaxComplete(
		
			function()
			{
		
				<?php if($form->getObject()->isNew()): ?>		
				if ( $( "#gtu_newVal_0_sub_group_name" ).length ) 
				{ 
					$('#gtu_newVal_0_sub_group_name').val("Country");
					if(!boolAdministrativeArea2)
					{
						addGroup("administrative area");
						boolAdministrativeArea2=true;
					}
				}
				if ( $( "#gtu_newVal_1_sub_group_name" ).length ) 
				{ 
					$('#gtu_newVal_1_sub_group_name').val("Municipality");
					if(!boolArea3)
					{
						addGroup("area");
						boolArea3=true;
					}
					
				}
				
				if ( $( "#gtu_newVal_2_sub_group_name" ).length ) 
				{ 
					$('#gtu_newVal_2_sub_group_name').val("Exact site");					
					
				}
                
                if ( $( "#gtu_newVal_3_sub_group_name" ).length ) 
				{ 
					$('#gtu_newVal_3_sub_group_name').val("ecology");                  
					
				}
				<?php endif; ?>	
				
				
			}
		);

</script>
