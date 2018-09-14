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
<!--rmca 2018 05 04-->
<table style="margin-top:20px; margin-bottom: 20px">
	<tr>
		<th class="top_aligned"><?php echo __("Country") ?></th>
		<td>          
			<?php echo $form['iso3166_text']->renderError() ?>
			<?php echo $form['iso3166_text'] ?>
			<?php echo __("ISO 3166-1") ?>
			<?php echo $form['iso3166']->renderError() ?>
			<?php echo $form['iso3166'] ?>
		</td>
	</tr>
	<tr>
		<th class="top_aligned"><?php echo __("Administrative subdivision") ?></th>
		<td>
			<?php echo $form['iso3166_subdivision_text']->renderError() ?>
			<?php echo $form['iso3166_subdivision_text'] ?>
			<?php echo __("ISO 3166-2") ?>
			<?php echo $form['iso3166_subdivision']->renderError() ?>
			<?php echo $form['iso3166_subdivision'] ?>
		</td>
	</tr>
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
		<!--	------------------------------------------------------------------------------------------------------------ -->
   <table style="width:100%">
		<!--JMHerpers 2018 07 02 added openlayers map-->
		<tr style="background-color:#D7F5D4">
			<td colspan="4" id="ol_map">
				<style >
					p.collapse{
						display:none;
					}
				</style>

				<div style="display: None">
					<?php echo $form['latitude_dms_degree'];?>
					<?php echo $form['latitude_dms_minutes'];?>
					<?php echo $form['latitude_dms_seconds'];?>
					<?php echo $form['latitude_dms_direction'];?>
					<?php echo $form['longitude_dms_degree'];?>
					<?php echo $form['longitude_dms_minutes'];?>
					<?php echo $form['longitude_dms_seconds'];?>
					<?php echo $form['longitude_dms_direction'];?>
					<?php echo $form['latitude_utm'];?>
					<?php echo $form['longitude_utm'];?>
					<?php echo $form['utm_zone'];?>
					<?php echo $form['latitude'];?>
					<?php echo $form['longitude'];?>
					<?php echo $form['geom_type'];?>
				</div>

				<table>
					<tr>
						<td colspan="2">
							<!--DMS/DD selector  ftheeten 2015 05 05-->
							<b><?php echo $form['coordinates_source']->renderLabel() ;?>
							   <?php echo $form['coordinates_source']->renderError() ?>:
							</b>
						</td>
						<td>
							<?php echo $form['coordinates_source'];?>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<label><b>Geometry type : &nbsp;</b></label>
						</td>
						<td>
							 <form class="form-inline">
								 <select class="typegeom" id="type">
									<option value="none"></option>
									<option value="point">Point</option>
									<option value="linestring">Linestring</option>
									<option value="polygon">Polygon</option>
									<option value="polygon2">Rectangle from 2 points</option>
									<!--<option value="Circle">Circle</option>-->
								  </select>				  
							  <label class="points_for_geometry"></label>
							</form>
						</td>
					</tr>
					<tr>
						<th colspan="3">
							<label>Enter coordinates :</label>
						</th>
					</tr>
					<tr>
						<td width=20"></td>
						<td colspan="2">
							<form class="list_points">
								<div class="GroupDMS" style="display: None">
									<table>
										<tr>
											<td colspan="7"><b>Latitude:</b></td><td colspan="7"><b>Longitude:</b></td>
										</tr>
										<tr>
											<td><input class="lati_deg_0 vsmall_size" type="text" maxlength = "3" value=0></td>
											<td>deg.</td>
											<td><input class="lati_min_0 vsmall_size" type="text" maxlength="2" value=0></td>
											<td>min.</td>
											<td><input class="lati_sec_0 vsmall_size" type="text" maxlength="8" value=0></td>
											<td>sec.</td>
											<td style="border-right: 1px solid #000000;">
												<select class="latns_0">
													<option value="N">N</option>
													<option value="S">S</option>
												</select>
											</td>
											<td>&nbsp;<input class="longi_deg_0 vsmall_size" type="text" maxlength="2" value=0></td>
											<td>deg.</td>
											<td><input class="longi_min_0 vsmall_size" type="text" maxlength="2" value=0></td>
											<td>min.</td>
											<td><input class="longi_sec_0 vsmall_size" type="text" maxlength="8" value=0></td>
											<td>sec.</td>
											<td><select class="longew_0">
													<option value="E">E</option>
													<option value="W">W</option>
												</select>
											</td>
										</tr>
									</table>
								</div>
								<div class="GroupDD" style="display: None">
									<table>
										<tr>
											<th>Latitude:</th>
											<th>&nbsp;Longitude:</th>
										</tr>
										<tr>
											<td style="border-right: 1px solid #000000;">
												<input class="latiDD_0 small_size" type="text" maxlength="12" value=0>
											</td>
											<td>
												&nbsp;<input class="longiDD_0 small_size" type="text" maxlength="12" value=0>
											</td>
										</tr>
									</table>
								</div>
								<div class="GroupUTM" style="display: None">
									<table>
										<tr>
											<th>X (meters):</th>
											<th>&nbsp;Y (meters):</th>
											<th>Zone:</th>
											<!--<th><?php echo $form['utm_zone']->renderLabel(); ?><?php echo $form['utm_zone']->renderError() ?></th>-->
										</tr>
										<tr>
											<td>
												<input class="longiUTM_0 small_size" type="text" maxlength="12" value=0>
											</td>
											<td>
												&nbsp;<input class="latiUTM_0 small_size" type="text" maxlength="12" value=0>
											</td>
											<td>
												<input class="zoneUTM_0" type="integer" size="2" min="1" max="60">
												<select class="dirUTM_0">
													<option value="S">S</option>
													<option value="N">N</option>
												</select>
												<!--<?php echo $form['utm_zone'];?>-->
											</td>
										</tr>
									</table>
								</div>
								<BR><div class="GroupDMS" id="list_of_coordDMS"></div>
								<div class="GroupDD" id="list_of_coordDD"></div>
								<div class="GroupUTM" id="list_of_coordUTM"></div>
								<!--<BR><input onclick="generate_wkt_vector()" class="btn_drawlines" type="button" value="Draw on map">-->
							</form>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<b><?php echo $form['lat_long_accuracy']->renderLabel() ;?>:<?php echo $form['lat_long_accuracy']->renderError() ?></b>
						</td>
						<td>
							<?php echo $form['lat_long_accuracy'];?>
							<strong><?php echo __('m');?></strong>
						</td>
					</tr>
					<tr>
						<td></td>
						<th colspan="2">
							<label>Show WKT and EPSG fields : </label><input type="checkbox" name="ShowWKT" class="ShowWKT" value="show">
						</th>
					</tr>
					<tr style="display:none;" class="WKT_show">
						<td></td>
						<th>
							<!--<div id="wkt">-->
							<label><?php echo $form['wkt_str']->renderLabel(); ?>:</label>
						</th>
						<td>
							<?php echo $form['wkt_str'];?>
						</td>
					</tr>
					<tr style="display:none;" class="WKT_show">
						<td></td>
						<th>
							<label><?php echo $form['original_coordinates']->renderLabel(); ?>:</label>
						</th>
						<td>
							<div style="display: None">
								<?php echo $form['original_coordinates'];?>
							</div>
							<label class="origcoordRO"></label>
						</td>
					</tr>
					<tr style="display:none;" class="WKT_show">
						<td></td>
						<th>
							<br/>EPSG : 
						</th>
						<td>
							<?php echo $form['epsg'];?>
						</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<br/><input type="button" name="wkt_to_map" id="wkt_to_map" value="Draw to map"/>
							<!--</div>-->
						</td>
					</tr>
					<tr>
						<th colspan="3">
							<label>Or draw directly on map :</label>
						</th>
					</tr>
				</table>
			</td>
		</tr>
		<!-------------------------------------------------------------------------------------------------------------- -->
		<!--JMHerpers 2018 07 02 added openlayers map-->
		<tr style="background-color:#D7F5D4">
			<td colspan="4" id="ol_map">
				<style >
					p.collapse{
						display:none;
					}
				</style>
				<div id="map_container_nh" class="map_container_nh">
					<div  style="width:500px;height:400px;" id="map" class="map"></div>
					<div id="mouse-position"></div>
				</div>
			</td>
		</tr>
   	    <tr style="background-color:#D7F5D4">
			<td colspan="4">
			     <select id="layer-select">
				   <option value="Aerial">Aerial</option>
				   <option value="AerialWithLabels" selected>Aerial with labels</option>
				   <option value="Road">Road (static)</option>
				   <option value="RoadOnDemand">Road (dynamic)</option>
				 </select>
			</td>
		</tr>
		<tr>
			<td colspan="4">
			</td>
		</tr>
		<!--<tr style="background-color:#D7F5D4">
			
			<th colspan="3"><?php echo $form['elevation_unit']->renderLabel(); ?></th>
		</tr>-->
		<tr style="background-color:#D7F5D4">
			<th><?php echo $form['elevation_max']->renderLabel(); ?><?php echo $form['elevation']->renderError() ?><?php echo $form['elevation_max']->renderError() ?><?php echo $form['elevation_unit']->renderError() ?>:</th>
			<td>From <?php echo $form['elevation'];?> to <?php echo $form['elevation_max'];?>m</td>
			<!--<td><?php echo $form['elevation_unit'];?></td>-->
			<th colspan="2"><?php echo $form['elevation_accuracy']->renderLabel() ;?><?php echo $form['elevation_accuracy']->renderError() ?>:&nbsp;<?php echo $form['elevation_accuracy'];?>m</th>
		</tr>
		<tr style="background-color:#D7F5D4">
			<th><?php echo $form['depth_min']->renderLabel(); ?><?php echo $form['depth_min']->renderError() ?><?php echo $form['depth_max']->renderError() ?>:</th>
			<td>From <?php echo $form['depth_min'];?> to <?php echo $form['depth_max'];?>m</td>
			<th colspan="2"><?php echo $form['depth_accuracy']->renderLabel() ;?><?php echo $form['depth_accuracy']->renderError() ?>:&nbsp;<?php echo $form['depth_accuracy'];?>m</th>
		</tr>
		
		<tr>
			<td colspan="4"></td>
		</tr>
		<tr style="background-color:#D7F5D4">
			<th><?php echo $form['ecosystem']->renderLabel(); ?><?php echo $form['ecosystem']->renderError() ?>:</th>
			<td colspan="3"><?php echo $form['ecosystem'];?></td>
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
	var controls;
	var wktfeaturegeom;
	var wktfromdata;
	var nbrpoints;
	var valueType = 'none';
	var vectorlayer;
	var mousePositionControl;
	var scaleLineControl;
	var map;
//	var bingBackground;
	var styleLine;
	var source;
	var vectorLoaded =false;
	var draw;
	var iLayer=0;
	var p_data_epsg= 'EPSG:4326';
	var source_selected;
	var typegeom;
    
    var main_layer_global;
	
	//ftheeten 2018 03 15
	<?php if($form->getObject()->isNew()): ?>
		var boolAdministrativeArea2=false;
		var boolArea3=false;
		var boolArea4=false;
		var boolHabitat5=false;
		var iso3166Selected;
	<?php endif; ?>
	//ftheeten 2016 02 05
	$( "form" ).submit(function( event ) {
	  GenWKT_and_draw(1);
	  window.opener.$("#gtu_filters_code").val($("#gtu_code").val());
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
			});
	
	function removeDarwinLayer(p_max){		
		if(vectorLoaded){
			map.getLayers().forEach(function(layer) {	
				if (typeof layer !== 'undefined') {			
					if(layer.get("name")!="background"&&parseInt(layer.get("name"))<p_max ){						
						map.removeLayer(layer);
					}
				}
			});
		}		
	}
	
	$("#wkt_to_map").click(
		function(){
			GenWKT_and_draw(1);
			/*//if($('.wkt').val().length == 0){
				generate_wkt_vector();
			//}
			//var format = new ol.format.WKT();
			var wkt_epsg=$("#gtu_wkt_epsg").val();		
			var wkt_str=$('.wkt').val(); //$("#gtu_wkt_str").val();

			if(wkt_str.length>0 && wkt_epsg.length>0)
			{
				draw_wkt(wkt_str, wkt_epsg);
			}*/
		}
	);
	
	function GenWKT_and_draw(origin){
		//if($('.wkt').val().length == 0){
			if(origin == 1){ //to prevent generation of wkt at form loading
				generate_wkt_vector();
			}
			//}
			//var format = new ol.format.WKT();
			//var wkt_epsg=$("#gtu_wkt_epsg").val();		
			//var wkt_epsg="EPSG:"+$(".epsg").val().substring(0,$(".epsg").val().indexOf('-'));	
			var wkt_epsg="EPSG:"+ $(".epsg").val();

            

			var wkt_str=$('.wkt').val(); //$("#gtu_wkt_str").val();
			if(wkt_str.length>0 && wkt_epsg.length>0)
			{
                    $.get( 'http://'+window.location.hostname+"/public.php/search/Convert4326WKT", { wkt: wkt_str, srid:$(".epsg").val()} )
                      .done(function( wkt_4326 ) {                        
                        draw_wkt(wkt_4326, "EPSG:4326");
                      });
                  
                
                //alert( window.location.hostname+"/public.php/search/Convert4326WKT");
				//draw_wkt(wkt_str, wkt_epsg);
			}
	}
    
    
	
	function draw_wkt(p_featureWKT, p_epsg)
	{
		var format = new ol.format.WKT();
		var featureWKT = format.readFeature(p_featureWKT, {
				dataProjection: p_epsg,
				featureProjection: 'EPSG:3857'
			});
			
		p_data_epsg=p_epsg;
		addDarwinLayer(featureWKT,"from values");	
	}
	
	function addDarwinLayer(feature,origininput)
	{
		 var tmp_geom;
		 
		 switch (valueType)
		{
			case "point":
				tmp_geom=new ol.geom.Point(feature.getGeometry().getCoordinates());
				generic_feature = new ol.Feature({geometry: tmp_geom});
				break;
			case "linestring":
				tmp_geom=new ol.geom.LineString(feature.getGeometry().getCoordinates());
				generic_feature = new ol.Feature({geometry: tmp_geom});
				break;
			case "polygon": 
			   tmp_geom=new ol.geom.Polygon(feature.getGeometry().getCoordinates());
			   generic_feature = new ol.Feature({geometry: tmp_geom});
			   break;
			 default :
			 alert("default");
				generic_feature=feature;
				tmp_geom=generic_feature.getGeometry();				
				break;
		}
		var tmpSource=new ol.source.Vector();
		tmpSource.addFeature(generic_feature);
		iLayer++;
		var vectorlayer_local = new ol.layer.Vector({
			        name: iLayer,
					source: tmpSource,
					style: styleWKT	});
					
		//vectorlayer	=vectorlayer_local;	

		map.addLayer(vectorlayer_local);
        //main_layer_global=vectorlayer_local;
		removeDarwinLayer(iLayer);
		
		if (origininput == "from drawing"){
			var format = new ol.format.WKT();
			tmp_geom4326= tmp_geom.clone();
			tmp_geom4326.transform("EPSG:3857", "EPSG:4326");
			wktfeaturegeom = format.writeGeometry(tmp_geom4326);
			$('.wkt').val(wktfeaturegeom);
			fill_points_lines_from_wkt(1);
            
             //alert(tmp_geom4326.getExtent());
         //   map.getView().setCenter(vectorlayer_local.getGeometry().getExtent());  
       // map.getView().setZoom(7);
           
		}
        
        //alert(tmp_geom.getCenter());
        //map.getView().fit(tmp_geom.getExtent());  
        
        map.getView().fit(vectorlayer_local.getSource().getExtent());
        map.getView().setZoom(7);
		vectorLoaded=true;		
	}
	
	function TransformStrTypeWithUppercase(type){
		if(type=="none"){
			return "None";
		}else if(type=="point"){
			return "Point";
		}else if(type=="linestring"){
			return "LineString";
		}else if(type=="polygon"){
			return "Polygon";
		}else{return "";}
	}
	
	function addInteraction() {
		//var typegeom;
		if (valueType !== 'none') {
			removeDarwinLayer();
			draw=null;
			if (valueType == "polygon2"){
				typegeom = "polygon";
			}else{
				typegeom = valueType;
			}
			draw = new ol.interaction.Draw({
				source: new ol.source.Vector(),
				type: TransformStrTypeWithUppercase(typegeom)
			});
			map.addInteraction(draw);
			draw.on('drawend', function (event) {
				addDarwinLayer(event.feature,"from drawing");
			});
		}
	}
	
	const typeSelect =  document.getElementById('type');

	typeSelect.onchange = function() {
		$('#list_of_coordDMS').html("");
		$('#list_of_coordDD').html("");
		$('#list_of_coordUTM').html("");
		//create new fields to enter coordinates
		valueType = typeSelect.value;
		switch (valueType){
			case "linestring": 
				$('.points_for_geometry').html('<form class="form_points">of <input class="nbrpoints" type="integer" name="points" maxlength="2" size="3"> points</form>');
			   break;
			case "polygon": 
				$('.points_for_geometry').html('<form class="form_points">of <input class="nbrpoints" type="integer" name="points" maxlength="2" size="3"> points </form>');
			   break;
			case "polygon2": 
				$('.points_for_geometry').html('<i>Enter latitude-longitude of points A and B:</i><img src="/images/rect_2points.jpg">');
				draw_points_input_fields(2);
				break;
			default:
				$('.points_for_geometry').html('');
		}
		$('.nbrpoints').focusout(function() {
			draw_points_input_fields($('.nbrpoints').val());
		});
		//IMPORTANT DO NOT MOVE THIS OR LAYERS ARE NOT DELETED
		map.removeInteraction(draw);
		addInteraction();
	};	  
	
	//JMHerpers 2018 07 09
	function draw_points_input_fields(nbrpointsin){
		/*if(valueType == "polygon2"){
			nbrpoints = 2;
		}else{
			nbrpoints = $('.nbrpoints').val();
		}*/
		//if ($('.nbrpoints').val() != ""){
		if (nbrpointsin != ""){
			
			//if(valueType == "polygon"){
				//nbrpoints2 = parseInt(nbrpoints)+1;
			//}
		//	var htmlcontent = '<form class="list_points"><table><tr><td colspan="7"><b>Latitude:</b></td><td colspan="7"><b>Longitude:</b></td></tr>';
			//List of fields for DMS-------------
			var htmlcontent = '<table>';
			for (i=1;i<nbrpointsin;i++){
				htmlcontent = htmlcontent + '<tr>';
				htmlcontent = htmlcontent + '<td><input class="lati_deg_'+i+' vsmall_size" type="text" maxlength = "3" value=0></td>';
				htmlcontent = htmlcontent + '<td>deg.</td>';
				htmlcontent = htmlcontent + '<td><input class="lati_min_'+i+' vsmall_size" type="text" maxlength="2" value=0></td>';
				htmlcontent = htmlcontent + '<td>min.</td>';
				htmlcontent = htmlcontent + '<td><input class="lati_sec_'+i+' vsmall_size" type="text" maxlength="8" value=0></td>';
				htmlcontent = htmlcontent + '<td>sec.</td>';
				htmlcontent = htmlcontent + '<td style="border-right: 1px solid #000000;"  ><select class="latns_'+i+'">';
				htmlcontent = htmlcontent + '		<option value="N">N</option>';
				htmlcontent = htmlcontent + '		<option value="S">S</option>';
				htmlcontent = htmlcontent + '	</select></td>';
				htmlcontent = htmlcontent + '<td>&nbsp;<input class="longi_deg_'+i+' vsmall_size" type="text" maxlength="2" value=0></td>';
				htmlcontent = htmlcontent + '<td>deg.</td>';
				htmlcontent = htmlcontent + '<td><input class="longi_min_'+i+' vsmall_size" type="text" maxlength="2" value=0></td>';
				htmlcontent = htmlcontent + '<td>min.</td>';
				htmlcontent = htmlcontent + '<td><input class="longi_sec_'+i+' vsmall_size" type="text" maxlength="8" value=0></td>';
				htmlcontent = htmlcontent + '<td>sec.</td>';
				htmlcontent = htmlcontent + '<td><select class="longew_'+i+'">';
				htmlcontent = htmlcontent + '		<option value="E">E</option>';
				htmlcontent = htmlcontent + '		<option value="W">W</option>';
				htmlcontent = htmlcontent + '	</select></td>';
				htmlcontent = htmlcontent + '</tr>';
			};
			//htmlcontent = htmlcontent + '</table><BR><input onclick="generate_wkt_vector()" class="btn_drawlines" type="button" value="Draw on map"></form>';
			htmlcontent = htmlcontent + '</table>';
			$('#list_of_coordDMS').html(htmlcontent);
			
			//List of fields for DD-------------
			var htmlcontent2 = '<table>';
			for (i=1;i<nbrpointsin;i++){
				htmlcontent2 = htmlcontent2 + '<tr>';
				htmlcontent2 = htmlcontent2 + '<td style="border-right: 1px solid #000000;"><input class="latiDD_'+i+' small_size" type="text" maxlength="12" value=0></td>';
				htmlcontent2 = htmlcontent2 + '<td>&nbsp;<input class="longiDD_'+i+' small_size" type="text" maxlength="12" value=0></td>';
				htmlcontent2 = htmlcontent2 + '</tr>';
			};

			htmlcontent2 = htmlcontent2 + '</table>';
			$('#list_of_coordDD').html(htmlcontent2);
			
			//List of fields for UTM-------------
			var htmlcontent3 = '<table>';
			for (i=1;i<nbrpointsin;i++){
				htmlcontent3 = htmlcontent3 + '<tr>';
				htmlcontent3 = htmlcontent3 + '<td>';
				htmlcontent3 = htmlcontent3 + '&nbsp;<input class="longiUTM_'+i+' small_size" type="text" maxlength="12" value=0>';
				htmlcontent3 = htmlcontent3 + '</td>';
				htmlcontent3 = htmlcontent3 + '<td>';
				htmlcontent3 = htmlcontent3 + '<input class="latiUTM_'+i+' small_size" type="text" maxlength="12" value=0>';
				htmlcontent3 = htmlcontent3 + '</td>';
				htmlcontent3 = htmlcontent3 + '<td>';
				htmlcontent3 = htmlcontent3 + '<input class="zoneUTM_'+i+'" type="integer" size="2" min="1" max="60">';
				htmlcontent3 = htmlcontent3 + '<select class="dirUTM_'+i+'">';
				htmlcontent3 = htmlcontent3 + '	<option value="S">S</option>';
				htmlcontent3 = htmlcontent3 + '	<option value="N">N</option>';
				htmlcontent3 = htmlcontent3 + '</select>';
				htmlcontent3 = htmlcontent3 + '</td>';
				htmlcontent3 = htmlcontent3 + '</tr>';
			};

			htmlcontent3 = htmlcontent3 + '</table>';
			$('#list_of_coordUTM').html(htmlcontent3);
		}
	}
	
	//JMHerpers 2018 07 06
	function generate_wkt_vector(){
		var errorval = false;
		//var typegeom;
		var originalcoords = "";
		var latDeci0= 0;
		var longDeci0= 0;
		if (valueType == 'none' | valueType == 'point') {
			typegeom = "point";
			nbrpoints = 1;
			$('.typegeom').val("point").change();
		}else if (valueType == 'polygon2') {
			typegeom = "polygon";
			nbrpoints = $('.nbrpoints').val();
		}else{
			typegeom = valueType;
			nbrpoints = $('.nbrpoints').val();
		}

		if (source_selected == "DMS"){
			for (i=0;i<nbrpoints;i++){
				if(((!$.isNumeric($('.lati_deg_'+i).val()) | !$.isNumeric($('.lati_min_'+i).val()) | !$.isNumeric($('.lati_sec_'+i).val()) | 
				   !$.isNumeric($('.longi_deg_'+i).val()) | !$.isNumeric($('.longi_min_'+i).val()) | !$.isNumeric($('.longi_sec_'+i).val()) ) &
				   ( $('.lati_deg_'+i).val().length + $('.lati_min_'+i).val().length + $('.lati_sec_'+i).val().length + 
					 $('.longi_deg_'+i).val().length + $('.longi_min_'+i).val().length + $('.longi_sec_'+i).val().length != 0
				   ))|
				   	($('.lati_deg_'+i).val().length + $('.lati_min_'+i).val().length + $('.lati_sec_'+i).val().length + 
					 $('.longi_deg_'+i).val().length + $('.longi_min_'+i).val().length + $('.longi_sec_'+i).val().length == 0 & nbrpoints == 1)
				   |
					$('.lati_deg_'+i).val() < 0 | $('.lati_deg_'+i).val() > 90 | $('.longi_deg_'+i).val() < 0 | $('.longi_deg_'+i).val() > 180 |
					$('.lati_min_'+i).val() < 0 | $('.lati_min_'+i).val() > 59 | $('.longi_min_'+i).val() < 0 | $('.longi_min_'+i).val() > 59 |
					$('.lati_sec_'+i).val() < 0 | $('.lati_sec_'+i).val() > 59 | $('.longi_sec_'+i).val() < 0 | $('.longi_sec_'+i).val() > 59 
				){
					errorval = true;
				}
			}
			
			if(errorval == true){
				alert('There are errors in the DMS coordinates entered!');
			}else{
				//////calculate wkt

				var latSign0 ;
				var longSign0 ;
				var latSign1 ;
				var longSign1 ;
				
				wktfromdata = typegeom.toUpperCase() +"(";
				if (typegeom.toUpperCase() == "POLYGON"){
					wktfromdata = wktfromdata +"(";
				}
				if (valueType != 'polygon2') {
					latcalc = 0
					for (i=0;i<nbrpoints;i++){
						var latSign;
						var longSign;
						var latDeci = 0;
						var longDeci = 0;

						if($( '.latns_'+i+' option:selected' ).text() == "S"){
							latSign = -1;
						}else{
							latSign = 1;
						}
						if($( '.longew_'+i+' option:selected' ).text() == "W"){
							longSign = -1;
						}else{
							longSign = 1;
						}
						if(i==0){
							latSign0 = latSign;
							longSign0 = longSign;
						}
						
						latDeci= latSign * (parseFloat($('.lati_deg_'+i).val()) + ( parseFloat($('.lati_min_'+i).val())/60) + ( parseFloat($('.lati_sec_'+i).val())/3600));
						longDeci= longSign  * (parseFloat($('.longi_deg_'+i).val()) + ( parseFloat($('.longi_min_'+i).val())/60) + ( parseFloat($('.longi_sec_'+i).val())/3600));
						latDeci0= latSign0 * (parseFloat($('.lati_deg_0').val()) + ( parseFloat($('.lati_min_0').val())/60) + ( parseFloat($('.lati_sec_0').val())/3600));
						longDeci0= longSign0  * (parseFloat($('.longi_deg_0').val()) + ( parseFloat($('.longi_min_0').val())/60) + ( parseFloat($('.longi_sec_0').val())/3600));
						wktfromdata = wktfromdata +longDeci + " "  +  latDeci + ",";
						
						originalcoords = originalcoords 
						+ parseFloat($('.lati_deg_'+i).val()) +"°"+parseFloat($('.lati_min_'+i).val())+"'"+parseFloat($('.lati_sec_'+i).val())+"\""+$( '.latns_'+i+' option:selected' ).text()+" "
						+ parseFloat($('.longi_deg_'+i).val()) +"°"+parseFloat($('.longi_min_'+i).val())+"'"+parseFloat($('.longi_sec_'+i).val())+"\""+$( '.longew_'+i+' option:selected' ).text()
						+",";
					}
				}else{
					if($( '.latns_0'+' option:selected' ).text() == "S"){
						latSign0 = -1;
					}else{
						latSign0 = 1;
					}
					if($( '.longew_0'+' option:selected' ).text() == "W"){
						longSign0 = -1;
					}else{
						longSign0 = 1;
					}
					if($( '.latns_1'+' option:selected' ).text() == "S"){
						latSign1 = -1;
					}else{
						latSign1 = 1;
					}
					if($( '.longew_1'+' option:selected' ).text() == "W"){
						longSign1 = -1;
					}else{
						longSign1 = 1;
					}

					latDeci0=  latSign0 * (parseFloat($('.lati_deg_0').val()) + ( parseFloat($('.lati_min_0').val())/60) + ( parseFloat($('.lati_sec_0').val())/3600));
					longDeci0= longSign0  * (parseFloat($('.longi_deg_0').val()) + ( parseFloat($('.longi_min_0').val())/60) + ( parseFloat($('.longi_sec_0').val())/3600));
					
					latDeci2= latSign1 * (parseFloat($('.lati_deg_1').val()) + ( parseFloat($('.lati_min_1').val())/60) + ( parseFloat($('.lati_sec_1').val())/3600));
					longDeci2= longSign1  * (parseFloat($('.longi_deg_1').val()) + ( parseFloat($('.longi_min_1').val())/60) + ( parseFloat($('.longi_sec_1').val())/3600));
					latDeci1= latDeci0;
					longDeci1= longDeci2;
					latDeci3= latDeci2;
					longDeci3= longDeci0;
					wktfromdata = wktfromdata + longDeci0 + " " + latDeci0 + ","+ longDeci1 + " " + latDeci1 + ","+ longDeci2 + " " + latDeci2 + ","+ longDeci3 + " " + latDeci3 + ",";
					
					originalcoords = originalcoords 
						+ parseFloat($('.lati_deg_0').val()) +"°"+parseFloat($('.lati_min_0').val())+"'"+parseFloat($('.lati_sec_0').val())+"\""+$( '.latns_0'+' option:selected' ).text()+" "
						+ parseFloat($('.longi_deg_0').val()) +"°"+parseFloat($('.longi_min_0').val())+"'"+parseFloat($('.longi_sec_0').val())+"\""+$( '.longew_0'+' option:selected' ).text()+","
						
						+ parseFloat($('.lati_deg_0').val()) +"°"+parseFloat($('.lati_min_0').val())+"'"+parseFloat($('.lati_sec_0').val())+"\""+$( '.latns_0'+' option:selected' ).text()+" "
						+ parseFloat($('.longi_deg_1').val()) +"°"+parseFloat($('.longi_min_1').val())+"'"+parseFloat($('.longi_sec_1').val())+"\""+$( '.longew_1'+' option:selected' ).text()+","
						
						+ parseFloat($('.lati_deg_1').val()) +"°"+parseFloat($('.lati_min_1').val())+"'"+parseFloat($('.lati_sec_1').val())+"\""+$( '.latns_1'+' option:selected' ).text()+" "
						+ parseFloat($('.longi_deg_1').val()) +"°"+parseFloat($('.longi_min_1').val())+"'"+parseFloat($('.longi_sec_1').val())+"\""+$( '.longew_1'+' option:selected' ).text()+","
						
						+ parseFloat($('.lati_deg_1').val()) +"°"+parseFloat($('.lati_min_1').val())+"'"+parseFloat($('.lati_sec_1').val())+"\""+$( '.latns_1'+' option:selected' ).text()+" "
						+ parseFloat($('.longi_deg_0').val()) +"°"+parseFloat($('.longi_min_0').val())+"'"+parseFloat($('.longi_sec_0').val())+"\""+$( '.longew_0'+' option:selected' ).text()+",";
				}

				$(".convertDMS2DDLat").val(latDeci0);
				$(".convertDMS2DDLong").val(longDeci0);
				$(".DMSLatSign").val(latSign0);
				$(".DMSLongSign").val(longSign0);
				$(".geom_type").val(typegeom);
				
				$('.origcoord').val(originalcoords.substring(0, originalcoords.length-1));
				$('.origcoordRO').html(originalcoords.substring(0, originalcoords.length-1));
				
				if(typegeom == "polygon"){
					wktfromdata = wktfromdata + longDeci0 + " " + latDeci0 + "))";
				}
				
				wktfromdata = wktfromdata.substring(0, wktfromdata.length-1) + ")";
				$(".wkt").val(wktfromdata);
                //alert("CTR1");
				//centerMap(longDeci0, latDeci0);

			}
		}
		if (source_selected == "DD"){
			for (i=0;i<nbrpoints;i++){
				if(((!$.isNumeric($('.latiDD_'+i).val()) | !$.isNumeric($('.longiDD_'+i).val()) ) &
				    ( $('.latiDD_'+i).val().length + $('.longiDD_'+i).val().length != 0 )
				   )|
				   ($('.latiDD_'+i).val().length + $('.longiDD_'+i).val().length == 0 & nbrpoints == 1)
				   |
					$('.latiDD_'+i).val() < -90 | $('.latiDD_'+i).val() > 90 | $('.longiDD_'+i).val() < -180 | $('.longiDD_'+i).val() > 180 
				  ){
					errorval = true;
				}
			}
			if(errorval == true){
				alert('There are errors in the decimal coordinates entered!');
			}else{
				//////calculate wkt
				wktfromdata = typegeom.toUpperCase() +"(";
				if (typegeom.toUpperCase() == "POLYGON"){
					wktfromdata = wktfromdata +"(";
				}
				if (valueType != 'polygon2') {
					for (i=0;i<nbrpoints;i++){
						var latDeci = 0;
						var longDeci = 0;

						latDeci= $('.latiDD_'+i).val();
						longDeci= $('.longiDD_'+i).val();
						wktfromdata = wktfromdata + longDeci + " " + latDeci + ",";
						originalcoords  = originalcoords + $('.longiDD_'+i).val() + " " + $('.latiDD_'+i).val()+ ",";
					}
					$(".convertDMS2DDLat").val($('.latiDD_0').val());
					$(".convertDMS2DDLong").val($('.longiDD_0').val());
				}else{
					latDeci0= $('.latiDD_0').val();
					longDeci0= $('.longiDD_0').val();
					latDeci2= $('.latiDD_1').val();
					longDeci2= $('.longiDD_1').val();
					latDeci1= latDeci0;
					longDeci1= longDeci2;
					latDeci3= latDeci2;
					longDeci3= longDeci0;
					wktfromdata = wktfromdata + longDeci0 + " " + latDeci0 + ","+ longDeci1 + " " + latDeci1 + ","+ longDeci2 + " " + latDeci2 + ","+ longDeci3 + " " + latDeci3 + ",";
					originalcoords  = originalcoords + longDeci0 + " " + latDeci0 + ","+ longDeci1 + " " + latDeci1 + ","+ longDeci2 + " " + latDeci2 + ","+ longDeci3 + " " + latDeci3 + ",";
					
					$(".convertDMS2DDLat").val(latDeci0);
					$(".convertDMS2DDLong").val(longDeci0);
				}
				$(".geom_type").val(typegeom);
				if(typegeom == "polygon"){
					wktfromdata = wktfromdata + $('.longiDD_0').val() + " " + $('.latiDD_0').val() + "))";
				}
			
				wktfromdata = wktfromdata.substring(0, wktfromdata.length-1) + ")";
				
				$('.origcoord').val(originalcoords.substring(0, originalcoords.length-1));
				$('.origcoordRO').html(originalcoords.substring(0, originalcoords.length-1));
				
				$(".wkt").val(wktfromdata);
                //alert("CENTER2");
				//centerMap($('.longiDD_0').val(), $('.latiDD_0').val());

			}
		}
		if (source_selected == "UTM"){
			for (i=0;i<nbrpoints;i++){
				if(((!$.isNumeric($('.latiUTM_'+i).val()) | !$.isNumeric($('.longiUTM_'+i).val()) ) &
				    ( $('.latiUTM_'+i).val().length + $('.longiUTM_'+i).val().length != 0 )
				   )|
				   ($('.latiUTM_'+i).val().length + $('.longiUTM_'+i).val().length == 0 & nbrpoints == 1)
				   |
					$('.latiUTM_'+i).val() < 0 | $('.latiUTM_'+i).val() > 10000000 | $('.longiUTM_'+i).val() < 0 | $('.longiUTM_'+i).val() > 2000000
				  ){
					errorval = true;
				}
			}
			if(errorval == true){
				alert('There are errors in the UTM coordinates entered!');
			}else{
				//////calculate wkt
				var latUTM = 0;
				var longUTM = 0;
				var coordUTM;
				
				wktfromdata = typegeom.toUpperCase() +"(";
				if (typegeom.toUpperCase() == "POLYGON"){
					wktfromdata = wktfromdata +"(";
				}
				if (valueType != 'polygon2') {
					for (i=0;i<nbrpoints;i++){
						//coordUTM= convertUTM2($('.longiUTM_'+i).val(),$('.latiUTM_'+i).val(),$('.zoneUTM_'+i).val(),$('.dirUTM_'+i).val(),$('.epsg').val());
						coordUTM= Utm2Wgs($('.longiUTM_'+i).val(),$('.latiUTM_'+i).val(),$('.zoneUTM_'+i).val(),$('.dirUTM_'+i).val());
						latUTM = coordUTM[1].toFixed(4);
						longUTM = coordUTM[0].toFixed(4);

						wktfromdata = wktfromdata + longUTM + " " + latUTM + ",";
						originalcoords  = originalcoords + longUTM + " " + latUTM+ ",";

						if (i==0){
							$(".convertDMS2DDLat").val(latUTM);
							$(".convertDMS2DDLong").val(longUTM);
						}
					}
				}else{
					//coordUTM0= convertUTM2($('.longiUTM_0').val(),$('.latiUTM_0').val(),$('.zoneUTM_0').val(),$('.dirUTM_0').val(),$('.epsg').val());
					coordUTM0= Utm2Wgs($('.longiUTM_0').val(),$('.latiUTM_0').val(),$('.zoneUTM_0').val(),$('.dirUTM_0').val());
					latUTM0 = coordUTM0[1].toFixed(4);
					longUTM0 = coordUTM0[0].toFixed(4);
						
					//coordUTM2= convertUTM2($('.longiUTM_1').val(),$('.latiUTM_1').val(),$('.zoneUTM_1').val(),$('.dirUTM_1').val(),$('.epsg').val());
					coordUTM2= Utm2Wgs($('.longiUTM_1').val(),$('.latiUTM_1').val(),$('.zoneUTM_1').val(),$('.dirUTM_1').val());
					latUTM2 = coordUTM2[1].toFixed(4);
					longUTM2 = coordUTM2[0].toFixed(4);
					
					latUTM1= latUTM0;
					longUTM1= longUTM2;
					latUTM3= latUTM2;
					longUTM3= longUTM0;
					wktfromdata = wktfromdata + longUTM0 + " " + latUTM0 + ","+ longUTM1 + " " + latUTM1 + ","+ longUTM2 + " " + latUTM2 + ","+ longUTM3 + " " + latUTM3 + ",";
					originalcoords  = originalcoords + longUTM0 + " " + latUTM0 + ","+ longUTM1 + " " + latUTM1 + ","+ longUTM2 + " " + latUTM2 + ","+ longUTM3 + " " + latUTM3 + ",";
					
					$(".convertDMS2DDLat").val(latUTM0);
					$(".convertDMS2DDLong").val(longUTM0);
				}
				$(".geom_type").val(typegeom);
				if(typegeom == "polygon"){
					wktfromdata = wktfromdata + $('.convertDMS2DDLong').val() + " " + $('.convertDMS2DDLat').val() + "))";
				}
			
				wktfromdata = wktfromdata.substring(0, wktfromdata.length-1) + ")";
				
				$('.origcoord').val(originalcoords.substring(0, originalcoords.length-1));
				$('.origcoordRO').html(originalcoords.substring(0, originalcoords.length-1));
				
				$(".wkt").val(wktfromdata);
                //alert("CENTER3");
				//centerMap($('.convertDMS2DDLong').val(), $('.convertDMS2DDLat').val());
				//update_point_on_map($(".convertDMS2DDLat").val(),$(".convertDMS2DDLong").val(), null);
			}
		}
	};
	
	function Utm2Wgs( X,Y,zone,sn) {
		if (sn=='S')
		{
			Y = Y - 10000000;
		}
		X = X - 500000;
		var sa = 6378137.000000;
		var sb = 6356752.314245;

	   var e = Math.pow( Math.pow(sa , 2) - Math.pow(sb , 2) , 0.5 ) / sa;
	   var e2 = Math.pow( Math.pow( sa , 2 ) - Math.pow( sb , 2 ) , 0.5 ) / sb;
	   var e2cuadrada = Math.pow(e2 , 2);
	   var c = Math.pow(sa , 2 ) / sb;



	   var S = ( ( zone * 6 ) - 183 ); 
	   var lat =  Y / ( 6366197.724 * 0.9996 );                         
	   var v =  (c * 0.9996)/ Math.pow( 1 + ( e2cuadrada * Math.pow( Math.cos(lat), 2 ))  , 0.5 ) ;
	   var a = X / v;
	   var a1 = Math.sin( 2 * lat );
	   var a2 = a1 * Math.pow( Math.cos(lat), 2);
	   var j2 = lat + ( a1 / 2 );
	   var j4 = ( ( 3 * j2 ) + a2 ) / 4;
	   var j6 = ( ( 5 * j4 ) + ( a2 * Math.pow( Math.cos(lat) , 2) ) ) / 3;
	   var alfa = ( 3 / 4 ) * e2cuadrada;
	   var beta = ( 5 / 3 ) * Math.pow(alfa , 2);
	   var gama = ( 35 / 27 ) * Math.pow(alfa , 3);
	   var Bm = 0.9996 * c * ( lat - alfa * j2 + beta * j4 - gama * j6 );
	   var b = ( Y - Bm ) / v;
	   var Epsi = ( ( e2cuadrada * Math.pow(a , 2)) / 2 ) * Math.pow( Math.cos(lat) , 2);
	   var Eps = a * ( 1 - ( Epsi / 3 ) );
	   var nab = ( b * ( 1 - Epsi ) ) + lat;
	   var senoheps = ( Math.exp(Eps) - Math.exp(-Eps) ) / 2;
	   var Delt = Math.atan(senoheps / Math.cos(nab) );
	   var TaO = Math.atan(Math.cos(Delt) * Math.tan(nab));
	   var longitude = (Delt *(180 / Math.PI ) ) + S;
	   var latitude = ( lat + ( 1 + e2cuadrada* Math.pow(Math.cos(lat), 2) - ( 3 / 2 ) * e2cuadrada * Math.sin(lat) * Math.cos(lat) * ( TaO - lat ) ) * ( TaO - lat ) ) * (180 / Math.PI);
	   var coord=[];
	   coord[0] = longitude;
	   coord[1] = latitude;
	   return coord;
		//longitude.toString().concat(" ; " + latitude.toString()) ;
	}

	/*function convertUTM2(x,y,zone,direction,epsg_wgs){
		wgsval = epsg_wgs.substring(epsg_wgs.indexOf('-')+1,epsg_wgs.length );

		var dir="";
		if(direction=="S"){
			dir="+ south";
			y = -y;
		}

		var utm = "+proj=utm +zone="+zone+' '+dir+" units=m +no_defs ";
		var wgs = "+proj=longlat +ellps="+wgsval+" +datum="+wgsval+" +no_defs";
		var conv=proj4(utm,wgs,[x, y]);

		return conv;
	}*/

	function centerMap(longi, lati) {
		/*map.getView().setCenter(ol.proj.fromLonLat([parseFloat(longi),parseFloat(lati)]));  
		map.getView().setZoom(7);*/      
        map.getView().fit(main_layer_global.getSource().getExtent());
        map.getView().setZoom(7);
	}

	function drawmap(){
		mousePositionControl= new ol.control.MousePosition({
			 coordinateFormat: ol.coordinate.createStringXY(4),
			projection:p_data_epsg,
			className: "custom-mouse-position",
			target: document.getElementById("mouse-position"),
			undefinedHTML: "&nbsp;"
		});
		scaleLineControl = new ol.control.ScaleLine();
			
		/*bingBackground= new ol.layer.Tile({
			name:"background",
			visible: true,
			preload: Infinity,
			source: new ol.source.BingMaps({key:"Ap9VNKmWsntrnteCapydhid0fZxzbV_9pBTjok2rQZS4pi15zfBbIkJkvrZSuVnJ",  imagerySet:"AerialWithLabels" })
		});*/

		styleLine= new ol.style.Style({
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
		
		source = new ol.source.Vector();
		vectorlayer = new ol.layer.Vector({
			source: source,
			style:styleLine
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
				key: "Ap9VNKmWsntrnteCapydhid0fZxzbV_9pBTjok2rQZS4pi15zfBbIkJkvrZSuVnJ",
				imagerySet: styles[i]
				// use maxZoom 19 to see stretched tiles instead of the BingMaps
				// "no photos at this zoom level" tiles
				// maxZoom: 19
			  })
			}));
		}
	  
		layers [styles.length] = vectorlayer;

		map = new ol.Map({
				target: 'map',
				layers: layers,     //[bingBackground,vectorlayer],
				 
				view: new ol.View({
				  center: ol.proj.fromLonLat([15,-4]),
				  zoom: 7
				}),
				controls: ol.control.defaults({
						attributionOptions: ({collapsible: false})
				}).extend([mousePositionControl, scaleLineControl ])
		});
		
		var select = document.getElementById('layer-select');
		function onChange() {
			var style = select.value;
			for (var i = 0, ii = layers.length; i < ii; ++i) {
			  layers[i].setVisible(styles[i] === style);
			}
		}
		select.addEventListener('change', onChange);
		onChange();
	}
	
	function fill_points_lines_from_wkt(origin){
		
		var originalcoords = "";
		var wktval = $('.wkt').val();
		var wktval2 = wktval.substring(wktval.indexOf("(")+1, wktval.length-1);
		var arraypoints = wktval2.split(","); 
		
		if(valueType == "polygon2"){
			nbrpoints = arraypoints.length-1;
			draw_points_input_fields(nbrpoints);
		}
		
		valueType = wktval.substring(0,wktval.indexOf("(")).toLowerCase();
		$('.typegeom').val(valueType);
		$('.points_for_geometry').html('<form class="form_points">of <input class="nbrpoints" type="integer" name="points" maxlength="2" size="3"> points</form>');
		
		if (valueType == "polygon"){
			nbrpointsfromwkt = arraypoints.length-1;
		}else{
			nbrpointsfromwkt = arraypoints.length;
		}

		$('.nbrpoints').val(nbrpointsfromwkt);
		//if(origin==0){
			draw_points_input_fields(nbrpointsfromwkt);
		//}
		
		for (i=0;i<nbrpointsfromwkt;i++){
			var lat = arraypoints[i].substring(arraypoints[i].indexOf(" ")+1,arraypoints[i].length);
			var lng = arraypoints[i].substring(0,arraypoints[i].indexOf(" "));
			
			if(i==0){
				$(".convertDMS2DDLat").val(lat);
				$(".convertDMS2DDLong").val(lng);
				$(".geom_type").val(valueType);
                //alert("CENTER4");
				//centerMap(lng, lat);
			}
			
			if(i==0 & valueType == "polygon"){
				lng = lng.substring(1, lng.length); 
			}
			
			//DMS---------------------
			$(".latns_"+i +" option[value='N']").attr("selected", true);
			if(lat < 0){
				$(".latns_"+i +" option[value='S']").attr("selected", true);
			}/*	if(i==0){
					latsign = -1;
				}
			}else{
				if(i==0){
					latsign = 1;
				}
			}*/
			$(".longew_"+i +" option[value='E']").attr("selected", true);
			if(lng < 0){
				$(".longew_"+i +" option[value='W']").attr("selected", true);
			}/*	if(i==0){
					longsign = -1;
				}
			}else{
				if(i==0){
					longsign = 1;
				}
			}*/

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
			
			//DD----------------------				
			$('.latiDD_'+i).val(lat);
			$('.longiDD_'+i).val(lng);
			
			//UTM---------------------
			var result = Wgs2Utm(lng,lat);
			//ZoneNumber = Math.floor((parseInt(lng) + 180)/6) + 1;

			$('.latiUTM_'+i).val(result[1]);
			$('.longiUTM_'+i).val(result[0]);
			$('.zoneUTM_'+i).val(result[2]);
			if(lat < 0){
				$(".dirUTM_"+i +" option[value='S']").attr("selected", true);
			}
			else{
				$(".dirUTM_"+i +" option[value='N']").attr("selected", true);
			}
			
		}

		if(origin != 2){
		//	$('.origcoord').val(originalcoords.substring(0, originalcoords.length-1));
			$('.origcoordRO').html($('.origcoord').val());
		}
	};
	
	function Wgs2Utm( lan1,fi) {
		var a=6378137.000;
		var b=6356752.314;
		var f=(a-b)/a;
		var e2=Math.sqrt((Math.pow(a,2)-Math.pow(b,2))/Math.pow(b,2));
		var e=Math.sqrt((Math.pow(a,2)-Math.pow(b,2))/Math.pow(a,2));

		var zone;
		var lan0;
		if (lan1>0)
		{
			var zone=30+Math.ceil(lan1/6);
			lan0=Math.floor(lan1/6)*6+3;
		}
		else
		{
			var zone=30-Math.floor(Math.abs(lan1)/6);
			lan0=-Math.floor(Math.abs(lan1)/6)*6-3;
		}
		var lan=lan1-lan0;
		lan=lan*Math.PI/180;
		fi=fi*Math.PI/180;
		var N=a/Math.pow(1-Math.pow(e,2)*Math.pow(Math.sin(fi),2),0.5);
		var M=a*(1-Math.pow(e,2))/Math.pow((1-(Math.pow(e,2)*Math.pow(Math.sin(fi),2))),(3/2));
		var t=Math.tan(fi);
		var p=N/M;

		var k0=0.9996;

		var term1=Math.pow(lan,2)*p*Math.pow(Math.cos(fi),2)/2;
		var term2=Math.pow(lan,4)*Math.pow(Math.cos(fi),4)*(4*Math.pow(p,3)*(1-6*Math.pow(t,2))+Math.pow(p,2)*(1+24*Math.pow(t,2))-4*p*Math.pow(t,2))/24;
		var term3=Math.pow(lan,6)*Math.pow(Math.cos(fi),6)*(61-148*Math.pow(t,2)+16*Math.pow(t,4))/720;

		var Kutm=k0*(term1+term2+term3);

		term1=Math.pow(lan,2)*p*Math.pow(Math.cos(fi),2)*(p-Math.pow(t,2))/6;
		term2=Math.pow(lan,4)*Math.pow(Math.cos(fi),4)*(4*Math.pow(p,3)*(1-6*Math.pow(t,2))+Math.pow(p,2)*(1+8*Math.pow(t,2))-Math.pow(p,2)*Math.pow(t,2)+Math.pow(t,4))/120;
		term3=Math.pow(lan,6)*Math.pow(Math.cos(fi),6)*(61-479*Math.pow(t,2)+179*Math.pow(t,4)-Math.pow(t,6))/5040;

		var Xutm=500000+k0*lan*N*Math.cos(fi)*(1+term1+term2+term3);

		var A0=1-0.25*Math.pow(e,2)-3/64*Math.pow(e,4)-5/256*Math.pow(e,6);
		var A2=3/8*(Math.pow(e,2)+0.25*Math.pow(e,4)+15/128*Math.pow(e,6));
		var A4=15/256*(Math.pow(e,4)+0.75*Math.pow(e,6));
		var A6=35/3072*Math.pow(e,6);

		var sfi=a*(A0*fi-A2*Math.sin(2*fi)+A4*Math.sin(4*fi)-A6*Math.sin(6*fi));

		term1=Math.pow(lan,2)*N*Math.sin(fi)*Math.cos(fi)/2;
		term2=Math.pow(lan,4)*N*Math.sin(fi)*Math.pow(Math.cos(fi),3)*(4*Math.pow(p,2)+p-Math.pow(t,2))/24;
		term3=Math.pow(lan,6)*N*Math.sin(fi)*Math.pow(Math.cos(fi),5)*(8*Math.pow(p,4)*(11-24*Math.pow(t,2))-28*Math.pow(p,3)*(1-6*Math.pow(t,2))+Math.pow(p,2)*(1-32*Math.pow(t,2))-p*2*Math.pow(t,2)+Math.pow(t,4));
		var term4=Math.pow(lan,8)*N*Math.sin(fi)*Math.pow(Math.cos(fi),7)*(1385-3111*Math.pow(t,2)+543*Math.pow(t,4)-Math.pow(t,6));

		var Yutm=k0*(sfi+term1+term2+term3+term4);
		var sn='N';
		if (fi <0)
		{
			Yutm = 10000000 + Yutm;
			sn='S';
		}
		
		//Xutm.toString().concat(" ; " + Yutm.toString() + " "+zone.toString()+sn) ;
		var results = [];
		results[0] = Xutm.toString();
		results[1] = Yutm.toString();
		results[2] = zone.toString();
		results[3] = sn.toString();
		
		return results;
	}


	
	////////////////document ready
	$(document).ready(function () {	
	
		//JMHerpers 2018 07 02
		
		checkCoordSourceState();
		drawmap();
		var origin = 0;

		if($('.wkt').val().length != 0){
				
			GenWKT_and_draw(origin);
			fill_points_lines_from_wkt(origin);
			//generate_wkt_vector();
		}else{
			testlength = $(".DMSLatDeg").val() + $(".convertDMS2DDLat").val() + $(".UTMLat").val();
			if(testlength.length != 0){
				var originalcoords = "";
				var wktfromlatlong = "";
				var latsign = "S";
				var longsign = "W";
				var DMSLatSec = 0;
				var DMSLongSec = 0;
				if(source_selected=="DD"){
					$('.latiDD_0').val($(".convertDMS2DDLat").val());
					$('.longiDD_0').val($(".convertDMS2DDLong").val());
				}
				else if(source_selected=="DMS"){
					if($('.DMSLatSign').val() == 1){
						latsign="N";
					}
					if($('.DMSLongSign').val() == 1){
						longsign="E";
					}
					if($(".DMSLatSec").val() != ""){
						DMSLatSec = $(".DMSLatSec").val();
					}
					if($(".DMSLongSec").val() != ""){
						DMSLongSec = $(".DMSLongSec").val();
					}
					
					$('.lati_deg_0 ').val($('.DMSLatDeg').val());
					$('.lati_min_0 ').val($('.DMSLatMin').val());
					$('.lati_sec_0 ').val(DMSLatSec);
					$(".latns_0 option[value='"+latsign+"']").attr("selected", true);
					$('.longi_deg_0  ').val($('.DMSLongDeg').val());
					$('.longi_min_0  ').val($('.DMSLongMin').val());
					$('.longi_sec_0  ').val(DMSLongSec);
					$(".longew_0 option[value='"+longsign+"']").attr("selected", true);
				}
				else if(source_selected=="UTM"){
				}
				typegeom = "point";
				nbrpoints = 1;
				GenWKT_and_draw(1);
			}
		}

		$('.ShowWKT').click(function() {
			$(".WKT_show").toggle(this.checked);
		});

		//ftheeten 2016 02 05
		showDMSCoordinates=false;

		$('.tag_parts_screen .clear_prop').live('click', function(){
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
		$('.purposed_tags li').live('click', function(){
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

	   function purposeTags(event) {
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

		$('#add_group').click(function(event){
		   event.preventDefault();
		  selected_group = $('#groups_select option:selected').val();
		  addGroup(selected_group);
		});

		$('a.sub_group').live('click',function(event){
		  event.preventDefault();
		  addSubGroup( $(this).closest('fieldset').attr('alt'));
		});
		
		//ftheeten 2016 09 15
		checkCoordSourceState();
		<?php if($form->getObject()->isNew()): ?>
			//ftheeten 2018 03 15
			var initAdminstrativeGroupsOnLoad=function(){
				addGroup("administrative area");			
			}
			initAdminstrativeGroupsOnLoad();
		<?php endif; ?>
		
       $('.iso3166').autocomplete({
			source: function (request, response) {
				$.ajax({
					url: "<?php print(sfConfig::get('dw_natural_heritage_webservices_domain'));?>/natural_heritage_webservice/service_nh_iso3166.php",
					dataType: 'json',
					data: { query: "iso3166_list", namepattern:request.term },
					success: function (data) {
						data.unshift({'iso3166_code': request.term, 'iso3166_name': request.term});
						response(data.map(function (value) {
							return {
								'label': value.iso3166_name,
								'value': value.iso3166_code
							};  
						}));
					} 
				}); 
			},
			select : function(event, ui){
				event.preventDefault();
				set_iso3166(event, ui);
				$(".iso3166_value").val(ui.item.value);
				ui.item.value = ui.item.label;
				$('.iso3166').val(ui.item.value);              
				return false;
			},        
			minLength: 2
		});
		var set_iso3166= function(e, ui) {
			$( "select[name*='sub_group_name']" ).each( 
				function(){       				
					if($(this).val().toLowerCase()=="country"){
						var idx = $(this).attr('id').match(/\d+/)[0];                    
						$("#gtu_newVal_"+idx+"_tag_value").val(ui.item.label);
						$("#gtu_TagGroups_"+idx+"_tag_value").val(ui.item.label);   				
					}
				}
			);
		};
		$('.iso3166_subdivision').autocomplete({
			source: function (request, response) {
				$.ajax({
					url: "<?php print(sfConfig::get('dw_natural_heritage_webservices_domain'));?>/natural_heritage_webservice/service_nh_iso3166.php",
					dataType: 'json',
					data: { query: "subdivisions_list", namepattern:request.term, iso3166:$('.iso3166_value').val()},
					success: function (data) {
						data.unshift({'returned_code': request.term, 'returned': request.term});
						response(data.map(function (value) {
							return {
								'label': value.returned,
								'value': value.returned_code
							};  
						}));
					} 
				}); 
			},
			select : function(event, ui){
				  event.preventDefault();
				  set_iso3166_subdivision(event, ui);
				  $(".iso3166_subdivision_value").val(ui.item.value);
				  
				  ui.item.value = ui.item.label;
				  $('.iso3166_subdivision').val(ui.item.value);              
					return false;
			},          
			minLength: 2
		});
    
		var set_iso3166_subdivision= function(e, ui)  {
			$( "select[name*='sub_group_name']" ).each( 
				function(){                
					if($(this).val().toLowerCase()=="region or district"){
						var idx = $(this).attr('id').match(/\d+/)[0];                    
						$("#gtu_newVal_"+idx+"_tag_value").val(ui.item.label);
						$("#gtu_TagGroups_"+idx+"_tag_value").val(ui.item.label);
					}
				}
			);
		};
		
		//JMHerpers 2018 07 26
		$('.ecosystem').autocomplete({
			source: function (request, response) {
				$.ajax({
					url: "<?php print(sfConfig::get('dw_natural_heritage_webservices_domain'));?>/natural_heritage_webservice/service_nh_ecosystems.php",
					dataType: 'json',
					data: { query: "ecosystems_list", namepattern:request.term },
					success: function (data) {
						data.unshift({'ecosystem_name': request.term});
						response(data.map(function (value) {
							return {
								'label': value.ecosystem_name
							};  
						}));
					} 
				}); 
			},
			select : function(event, ui){
				event.preventDefault();
				ui.item.value = ui.item.label;
				$('.ecosystem').val(ui.item.value);              
				return false;
			},        
			minLength: 2
		});
	});

	function addSubGroup(selected_group, default_type, value){
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

	/*function addTagToGroup(group, sub_group, tag){
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
	}*/

	function disableUsedGroups(){
	  $('#groups_select option').removeAttr('disabled');
	  $('.tag_parts_screen fieldset:visible').each(function()
	  {
		var cur_group = $(this).attr('alt');
		$("#groups_select option[value='"+cur_group+"']").attr('disabled','disabled');
		if($("#groups_select option[value='"+cur_group+"']:selected"))
		  $('#groups_select').val("");
	  });
	}

	function addGroup(g_val, sub_group, value){
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
	function checkCoordSourceState(){
		source_selected=$( ".coordinates_source" ).val();
			
		var showDMS='display: table-cell';
		var showDD='display: None';
		var showUTM='display: None';
		/*var showDMS='display: table-cell';
		var showDD='display: table-cell';
		var showUTM='display: table-cell';*/
		if(source_selected=="DD")
		{
			showDMS='display: None';
			showDD='display: table-cell';
			showUTM='display: None';
		}
		else if(source_selected=="DMS")
		{
				   
			showDMS='display: table-cell';
			showDD='display: None';
			showUTM='display: None';
		}
		else if(source_selected=="UTM")
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
		function(){
			checkCoordSourceState();
			//JMHerpers 2018 09 11
			if($('.wkt').val().length != 0){
				fill_points_lines_from_wkt(2);
			}
		//$('.butShowDMS option[value='+source_selected+']').attr('selected','selected');
		}
	);

	function convertCoordinatesDMS2DD(){
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

	function convertCoordinatesDD2DMS(){
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

	$(".convertDMS2DDGeneralOnLeave").mouseleave(function(event){
		var idControl=event.target.id;
		var value=$("#"+idControl).val();
		if(value.trim().length>0)
		{
				convertCoordinatesDMS2DD();
				//changeCoordinateSource(0);
		}
	});

	$(".convertDMS2DDGeneralOnLeave").change(function(event){
			coordViewMode=false;
			//changeCoordinateSource(0);
	});


	$(".convertDD2DMSGeneral").change(function(event){
			coordViewMode=false;
			convertCoordinatesDD2DMS();
			//changeCoordinateSource(1);
	});


	$(".convertDMS2DDGeneralOnChange").change(function(event){
			coordViewMode=false;
			convertCoordinatesDMS2DD();
			//changeCoordinateSource(0);
	});


	$(".convertDD2DMSGeneral").mouseleave(function(event){
			var idControl=event.target.id;
			var value=$("#"+idControl).val();
			if(value.trim().length>0){
				convertCoordinatesDD2DMS();
				//changeCoordinateSource(1);
			}
		}
	);

	//ftheeeten 20150610
	//to prevent accidental updates of coordibates on mouseleave (as the  GTU are always displayed in "edit" mode)
	function detectBothValCoordExisting(){
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
	$(".UTM2DDGeneralOnLeave").change(function(event){
		convertUTM();
	});

	function convertUTM(){
			zone=$(".UTMZone").val();
			//zone="UTM WGS84 zone 31N";
			//zoneUTM=+proj=utm +zone=8431  +datum=WGS84 +units=m +no_defs
			
			var zoneUTM =  initUTM('tmp', zone.replace( /\D+/g, ''), zone.replace( /[0-9]*/g, ''));

			var wgs84=proj4('EPSG:4326');
			var lat=$(".UTMLat").val();
			var lng=$(".UTMLong").val();
			var conv=proj4(zoneUTM,wgs84,[lng,lat]);

			$(".convertDMS2DDLat").val(conv[1].toFixed(4));
			$(".convertDMS2DDLong").val(conv[0].toFixed(4));
		update_point_on_map($(".convertDMS2DDLat").val(),$(".convertDMS2DDLong").val(), null);
			
	}

	function initUTM(name, zone, direction ){
		var dir="";
		if(direction=="S")
		{
			dir="+ south";
		}
		var strProj='+proj=utm +zone='+zone+' '+dir+' +datum=WGS84 +units=m +no_defs ';

		return strProj;
	}

    //rmca 2016 06 21--
    $(".take_specimen_code").click(function(){
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
    $(".take_gtu_code").click( function(){
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
	$(".take_ig_code").click( function(){
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
	function update_point_on_map( lati, longi, accu){
		//JMHerpers 2018 07 02
		drawmap(vectorlayer, lati, longi);
		//showOL(lati,longi,1);
		
		//var latlng = L.latLng(lati, longi);
		//drawPoint(latlng, accu );

	}
	
	//ftheeten 2018 03 15 to add country, municipality and exact sites widgets
	var addTags= function(){
		if ( $( "#gtu_newVal_0_sub_group_name" ).length ) { 
			$('#gtu_newVal_0_sub_group_name').val("Country");
			if(!boolAdministrativeArea2){
				addGroup("administrative area");
				boolAdministrativeArea2=true;
			}
		}
			
		if ( $( "#gtu_newVal_1_sub_group_name" ).length ) { 
			$('#gtu_newVal_1_sub_group_name').val("Municipality");
			if(!boolArea3){
				addGroup("administrative area");
				boolArea3=true;
			}
		}
		
		if ( $( "#gtu_newVal_2_sub_group_name" ).length ) { 
			$('#gtu_newVal_2_sub_group_name').val("Region or district");
			if(!boolArea4){
				addGroup("area");
				boolArea4=true;
			}
		}
		
		if ( $( "#gtu_newVal_3_sub_group_name" ).length ) { 
			$('#gtu_newVal_3_sub_group_name').val("Exact site");
		   if(!boolHabitat5){
				addGroup("habitat");
				boolHabitat5=true;
			}				
		}
		
		if ( $( "#gtu_newVal_4_sub_group_name" ).length ) { 
			$('#gtu_newVal_4_sub_group_name').val("ecology");                  
		}
	}
		 
	$(document).ajaxComplete(		
		function(){
			<?php if($form->getObject()->isNew()&&strpos( $_SERVER['REQUEST_URI'],"new")&&strpos( $_SERVER['REQUEST_URI'],"duplicate_id")===FALSE): ?>		
				addTags();
			<?php endif; ?>	
		}
	);
	

</script>
