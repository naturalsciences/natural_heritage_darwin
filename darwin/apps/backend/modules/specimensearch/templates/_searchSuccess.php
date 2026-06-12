<div>
      <?php if($is_specimen_search):?>
        <input type="hidden" name="spec_search" value="<?php echo $is_specimen_search;?>" />
      <?php endif;?>
  <?php if(isset($specimensearch) && $specimensearch->count() != 0 && isset($orderBy) && isset($orderDir) && isset($currentPage)):?>   
    <?php
      if($orderDir=='asc')
        $orderSign = '<span class="order_sign_down">&nbsp;&#9660;</span>';
      else
        $orderSign = '<span class="order_sign_up">&nbsp;&#9650;</span>';
    ?>
    <?php
	 
	function init_map_json($spec_list, $code_list, &$has_coordinates)
	{
		$returned=Array();
		$returned["type"]="FeatureCollection";
		$returned["crs"]["type"]="name";
		$returned["crs"]["properties"]["name"]=["EPSG:4326"];
		
		$points=Array();
		foreach($spec_list as $spec)
		{
			
			if(is_numeric($spec->getLatitude())&&is_numeric($spec->getLongitude()))
			{
				$has_coordinates=true;
				$obj=Array();
				$obj["type"]="Feature";
				$obj["geometry"]["type"]="Point";
				$obj["geometry"]["coordinates"]=[(float)$spec->getLongitude(),(float)$spec->getLatitude()];
				$obj["properties"]["dw_id"]=addslashes($spec->getId());
				$obj["properties"]["dw_code"]="Undefined id";
				$obj["properties"]["dw_taxon_name"]="";
				if($spec->getTaxonName()!==null)
				{
					$obj["properties"]["dw_taxon_name"]=addslashes(preg_replace('/\t/',' ',preg_replace('/\xc2\xa0/', ' ',$spec->getTaxonName())));
				}
				foreach($code_list[$spec->getId()] as $key=>$code)
				{
					if($code->getCodeCategory() == 'main')
					{
						$obj["properties"]["dw_code"]=addslashes(preg_replace('/\t/',' ',str_replace(","," ", preg_replace('/\xc2\xa0/', ' ',$code->getFullCode()))));
					}
				}
				$points[]=$obj;
			}		
		}
		$returned["features"]=$points;
		return json_encode($returned);
	}
	$has_coordinates=false;
	$array_coords=init_map_json($specimensearch, $codes, $has_coordinates);
	
	/*
	$callback_coords=false;
	print("test1");
	if(array_key_exists("specimen_search_filters", $_REQUEST))
	{
		print("test2");
		if(array_key_exists("wkt_search", $_REQUEST["specimen_search_filters"]))
		{
			print("test3");
			$callback_coords=true;
			$has_coordinates=true;
			$array_coords= $_REQUEST["specimen_search_filters"]["wkt_search"];
			print($array_coords);
		}
	}
	if(!$callback_coords)
	{
		$array_coords=init_map_json($specimensearch, $codes, $has_coordinates);
	}
	*/

?>
    <!-- ftheeten 2014 04 17-->
    <?php include_partial('showurl', array('id'=>1, 'currentPage'=>$currentPage,'postMapper' => $_POST, 'getMapper' => $_GET, 's_url'=>$s_url , 'method'=>$_SERVER['REQUEST_METHOD'])); ?>
    <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
    <?php include_partial('pager_info', array('form' => $form, 'pagerLayout' => $pagerLayout, 'container'=> '.spec_results')); ?>
	<?php if($has_coordinates):?>
    <?php include_partial('map', array('form' => $form, "specimensearch"=> $specimensearch, "codes"=>$codes, "geojson"=>$array_coords));?>
    <?php endif;?>    
	<table class="spec_results">
        <thead>
          <tr>
            <th><!-- checkbox for selection of records to be removed -->
              <?php if($is_specimen_search):?>
                <label><input type="checkbox" class="top_remove_spec" /></label>
              <?php endif;?>
            </th>
            <th><!-- Pin -->
              <label class="top_pin"><?php print(__("Select all")); ?><input type="checkbox" /></label>
            </th>
             <th><?php print(__("Actions")); ?></th>
            <?php $all_columns = $columns->getRawValue() ;?>
            <?php foreach($all_columns as $col_name => $col):?>
              <th class="col_<?php echo $col_name;?>">
                <?php if($col[0] !== false):?>
                  <a class="sort" href="<?php echo url_for($s_url.'&orderby='.$col[0].( ($orderBy==$col[0] && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.
                    $currentPage);?>">
                    <?php echo $col[1];?>
                    <?php if($orderBy == $col[0]) echo $orderSign; ?>
                  </a>
                <?php else:?>                 
                <!--ftheeten 2018 09 06-->
                  <?php if($col_name == 'codes') : ?>
					<a class="sort" href="<?php echo url_for($s_url.'&orderby=main_code_indexed'.( ($orderBy=="main_code_indexed" && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.
                    $currentPage);?>">
						<?php echo $col[1];?>
						<?php if($orderBy == $col[0]) echo $orderSign; ?>
					</a>
                  <?php else:?>
						<?php echo $col[1];?>
                  <?php endif ; ?>
                 <?php endif ; ?>
              </th>
            <?php endforeach;?>
           
          </tr>
        </thead>
        <tbody>
        <?php foreach($specimensearch as $specimen):?>
            <tr class="rid_<?php echo $specimen->getId()?>">
              <td>
                <?php if($is_specimen_search):?>
                  <label><input type="checkbox" class="remove_spec" value="<?php echo $specimen->getId()?>"/></label>
                <?php endif;?>
              </td>
              <td>
                <label class="pin"><input type="checkbox" value="<?php echo $specimen->getId();?>" <?php if($sf_user->isPinned($specimen->getId(), 'specimen')):?>checked="checked"<?php endif;?> /></label>
              </td>
              <td>
               <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))), 'specimen/view?id='.$specimen->getId(), array('target' => 'pop'));?>
               <?php if($sf_user->isAtLeast(Users::ADMIN) || $specimen->getHasEncodingRights()) : ?>
                <?php echo link_to(image_tag('edit.png', array("title" => __("Edit"))), 'specimen/edit?id='.$specimen->getId(), array('target' => 'pop'));?>
                <?php echo link_to(image_tag('duplicate.png', array("title" => __("Duplicate"))), 'specimen/new?duplicate_id='.$specimen->getId(), array('class' => 'duplicate_link','target' => '_blank'));?>
              <?php endif; ?>
               
              </td>
              <?php include_partial('result_content_specimen', array( 'specimen' => $specimen, 'codes' => $codes, 'is_specimen_search' => $is_specimen_search)); ?>
              <?php include_partial('result_content_individual', array( 'specimen' => $specimen, 'is_specimen_search' => $is_specimen_search)); ?>
              <?php include_partial('result_content_part', array( 'specimen' => $specimen, 'is_specimen_search' => $is_specimen_search, 'loans' => $loans)); ?>
              
            </tr>
        <?php endforeach;?>
      </tbody>
      </table>
      <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
      <!-- ftheeten 2014 04 17-->
    <?php include_partial('showurl', array('id'=>2, 'currentPage'=>$currentPage,'postMapper' => $_POST, 'getMapper' => $_GET, 's_url'=>$s_url , 'method'=>$_SERVER['REQUEST_METHOD'])); ?>
    <?php else:?>
      <?php echo __('No Specimen Matching');?>
    <?php endif;?>
</div>  
<script type="text/javascript">

var ajax_initialized={}
var id_orthanc={};
var current_id_orthanc=null;
ajax_initialized["comments"]=false;


function pin(ids, status) {
  var id_part = "";
  if( Object.prototype.toString.call( ids ) === '[object Array]' ) {
    id_part = '/mid/' + ids.join(",");
  } else {
    id_part = '/id/' + ids;
  }
  $.getJSON('<?php echo url_for('savesearch/pin?source=specimen');?>'+ id_part + '/status/' + ( status ? '1':'0'),function (data){
    if(data.pinned) {
      $('.pinned_specimens i').text('(' + Object.keys(data.pinned).length + ')');
    }
  });
}

var ajaxForComments=function()
{
	//console.log("ajax_for_comments");
	var list_ids=Array();
	$(".pin :checkbox").each(function() { 
		  var tmp=$(this).val();
		  //console.log(tmp);
		  list_ids.push(tmp);
		});
	//console.log(list_ids);
	var url_comment="<?php print(url_for('comment/get_comments_json'));?>";
	//console.log(url_comment);
	data={}
	data["specimen_ids"]=JSON.stringify(list_ids);
	$.ajax({
	  type: "POST",
	  url: url_comment,
	  data: data,
	  dataType: "json",
	  success: function( result )
	  {
		  
		  //console.log(result);
		  $.each(result,function(id_dw,nested){ 
			//console.log(id_dw);
			//console.log(nested);
			//console.log("td.col_comments [value_dw='"+id_dw+"']");
			//var cell_comment=$("td.col_comments [value_dw='"+id_dw+"']");
			
			var tmp_html="<div class='general_gtu' style='display:grid'>";
			$.each(nested,function(concept,list_values)
			{
				//console.log(concept);
				//console.log(list_values);
				tmp_html=tmp_html+"<strong>"+concept+"</strong><ul class='name_tags_view'>";
				for(var i=0; i<list_values.length;i++)
				{
					tmp_html=tmp_html+"<li>"+list_values[i]+"</li>";
				}
				tmp_html=tmp_html+"</ul>";
			});
			//console.log(tmp_html);
			//$(cell_comment).html(tmp_html);
			tmp_html=tmp_html+"</div>";
			$(".col_comments[value_dw='"+id_dw+"']").html(tmp_html);
			//$(".spinner_comment").hide();
			
		  });
		  $(".spinner_comment").hide();
	  }
	});
	ajax_initialized["comments"]=true;
}


var display_orthanc_thumbnail=function(p_url, p_uuid)
{
	console.log("orthanc_thumbnail");
	console.log(p_url);
	console.log(p_uuid)
	p_url=p_url.replace("manifest.json","full/!/0/default.jpg");
	console.log(p_url);
	var tmp_html="<img src='"+p_url+"' style='max-width:200px; height:auto;'></img>";
	$(".specimen_orthanc_thumbnail[dw_uuid='"+p_uuid+"']").html(tmp_html);
	$(".specimen_orthanc_thumbnail[dw_uuid='"+p_uuid+"']").show();
}

var ajaxForLinks=function()
{
	var list_ids=Array();
	$(".pin :checkbox").each(function() { 
		  var tmp=$(this).val();
		  //console.log(tmp);
		  list_ids.push(tmp);
		});
	//console.log(list_ids);
	var url_links="<?php print(url_for('extlinks/get_ext_links_json'));?>";
	//console.log(url_comment);
	data={}
	data["specimen_ids"]=JSON.stringify(list_ids);
	$.ajax({
	  type: "POST",
	  url: url_links,
	  data: data,
	  dataType: "json",
	  success: function( result )
	  {
		  
		  //console.log(result);
		  $.each(result,function(id_dw,nested)
		  { 
			console.log(id_dw);
			console.log(nested);
			 $.each(nested,function(id_dw2,nested2)
			 {
				 console.log(nested2);
				 if(nested2["type"]=="2d_orthanc_general"&& nested2["access_rights"]=="public")
				 {
					 console.log("orthanc");
					 
					 var orthanc_url=nested2["url"];
					 console.log(orthanc_url);
					 var tmp_urls=Array();
					 tmp_urls.push(orthanc_url);
					 console.log("link_uuid");
					 console.log( nested2["uuid"]);
					 process_orthanc_iiif(tmp_urls, nested2["uuid"], display_orthanc_thumbnail, current_id_orthanc);
					 
				 }
			 });
		  }
		  );
		}
	  });
	
}

var additionalAjaxQueries=function()
{
	//console.log("test ajax search");
	if($('.col_comments').is(':visible'))
	{
		//console.log("comment is visible");
		ajaxForComments();
	}
	ajaxForLinks();
	
}

$(document).ready(function () {
  //Init screen size
  check_screen_size();

  //Init resize of screen
  $(window).resize(check_screen_size);

  //Init columns visibilty
  $('ul.column_menu .col_switcher :not(:checked)').each(function(){
    $('.col_' + $(this).val()).hide();
  });

  //Init custom checkbox
  //$('input[type=checkbox], input[type=radio]').not('label.custom-label input').customRadioCheck();
  
  // Init Top pin state
  if($('.pin :checked').length == $('.pin :checkbox').length) {
    $('.top_pin :checkbox').attr('checked','checked').trigger('update');
  }
  else {
    $('.top_pin :checkbox').attr('checked',false).trigger('update');
  }

  
  //Pin a specimen
  $('.spec_results .pin :checkbox').change(function(){
     pin($(this).val(), $(this).is(':checked'));
  });

  // Check all pin's on the page
  $('.spec_results .top_pin :checkbox').click(function(){
    $('.spec_results .pin :checkbox').attr('checked', $(this).is(':checked')).trigger('update');
    pins = [];
    $('.spec_results .pin :checkbox').each(function(){
      pins.push($(this).val());
    });
    pin(pins, $(this).is(':checked'));
  }); 

  // Check all "remove specimen" for saved spec search
  $('.spec_results .top_remove_spec').click(function(){
    $('.remove_spec').attr('checked', $(this).is(':checked')).trigger('change');
  });
  
  // Hide or Show more than 2 codes 
  $('.top_code :checkbox').change(function(){
    if($(this).is(':checked'))
     $('.code_supp').removeClass('hidden')
    else
     $('.code_supp').addClass('hidden')
  });
  
  additionalAjaxQueries();
});
</script>
