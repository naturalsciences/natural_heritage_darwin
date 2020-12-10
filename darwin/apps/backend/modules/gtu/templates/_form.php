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
	<tr>
        <th><?php echo $form['nagoya']->renderLabel() ?></th>
        <td>
			<?php echo $form['nagoya']->renderError() ?>
			<?php echo $form['nagoya'] ?>
			<a href=location.protocol + '//' + location.host + "/help/nagoya_countries.html" target="popup" onclick="window.open(location.protocol + '//' + location.host +  '/help/nagoya_countries.html','popup','width=1150,height=800'); return false;" style="display: inline-block;">
				<?php echo image_tag('info.png',"title=nagoya_info class=nagoya_info id=nagoya");?>
			</a> 
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
	<div>
		<!--DMS/DD selector  ftheeten 2015 05 05-->
		<b><?php echo $form['coordinates_source']->renderLabel() ;?><?php echo $form['coordinates_source']->renderError() ?></b><br/><?php echo $form['coordinates_source'];?>  <?php echo image_tag('remove.png', array("class"=> "delete_coords")); ;?>
		
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
		<tr>
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
  window.opener.$("#gtu_filters_code").val($("#gtu_code").val());
  });
 


$(document).ready(function () {
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
    
    //ftheeten 2018 05 04

 
        $('.iso3166').autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "http://darwin/natural_heritage_webservice/service_nh_iso3166.php",
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

        select : function(event, ui)
         {
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
        
            function()
            {                
                if($(this).val().toLowerCase()=="country")
                {
                    
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
                url: "http://darwin/natural_heritage_webservice/service_nh_iso3166.php",
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
        select : function(event, ui)
         {
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
        
            function()
            {                
                if($(this).val().toLowerCase()=="region or district")
                {
                    
                    var idx = $(this).attr('id').match(/\d+/)[0];                    
                    $("#gtu_newVal_"+idx+"_tag_value").val(ui.item.label);
                    $("#gtu_TagGroups_"+idx+"_tag_value").val(ui.item.label);
                }
            }
        );
    };
   
    


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
            var latlng = L.latLng(lati, longi);
            drawPoint(latlng, accu );
        }
		
		//ftheeten 2018 03 15 to add country, municipality and exact sites widgets
        var addTags= function()
         {
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
						addGroup("administrative area");
						boolArea3=true;
					}
					
				}
                
                if ( $( "#gtu_newVal_2_sub_group_name" ).length ) 
				{ 
					$('#gtu_newVal_2_sub_group_name').val("Region or district");
					if(!boolArea4)
					{
						addGroup("area");
						boolArea4=true;
					}
					
				}
                
        
				
				if ( $( "#gtu_newVal_3_sub_group_name" ).length ) 
				{ 
					$('#gtu_newVal_3_sub_group_name').val("Exact site");
                   if(!boolHabitat5)
					{
						addGroup("habitat");
						boolHabitat5=true;
					}				
					
				}
                
                if ( $( "#gtu_newVal_4_sub_group_name" ).length ) 
				{ 
					$('#gtu_newVal_4_sub_group_name').val("ecology");                  
					
				}
         }
        
		$(document).ajaxComplete(
		
			function()
			{
                    
                    
				<?php if($form->getObject()->isNew()&&strpos( $_SERVER['REQUEST_URI'],"new")&&strpos( $_SERVER['REQUEST_URI'],"duplicate_id")===FALSE): ?>		
                    addTags();
				<?php endif; ?>	
				
				
			}
		
		);
		
			
			$(".delete_coords").click(
				function()
				{
					console.log("delete");
					$(".convertDD2DMSGeneral").val("");
					$(".convertDMS2DDGeneralOnLeave").val("");
					$(".UTM2DDGeneralOnLeave").val("");
				}
			);
</script>
