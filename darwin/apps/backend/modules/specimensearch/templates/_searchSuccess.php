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
					$obj["geometry"]["coordinates"]=[(float)$spec->getLatitude(), (float)$spec->getLongitude()];
					$obj["properties"]["dw_id"]=$spec->getId();
					$obj["properties"]["dw_code"]="Undefined id";
					$obj["properties"]["dw_taxon_name"]="";
					if($spec->getTaxonName()!==null)
					{
						$obj["properties"]["dw_taxon_name"]=$spec->getTaxonName();
					}
					foreach($code_list[$spec->getId()] as $key=>$code)
					{
						if($code->getCodeCategory() == 'main')
						{
							$obj["properties"]["dw_code"]=str_replace(","," ", $code->getFullCode());
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
		

	?>
        <!--jm herpers 2018 06 04 removed show url-->
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
              <label class="top_pin"><input type="checkbox" /></label>
            </th>
			<th><!-- actions --></th>
            <?php $all_columns = $columns->getRawValue() ;?>
            <?php foreach($all_columns as $col_name => $col):?>
              <th class="col_<?php echo $col_name;?>">
                <?php if($col[0] != false):?>
                  <a class="sort" href="<?php echo url_for($s_url.'&orderby='.$col[0].( ($orderBy==$col[0] && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.
                    $currentPage);?>">
                    <?php echo $col[1];?>
                    <?php if($orderBy == $col[0]) echo $orderSign; ?>
                  </a>
                <?php else:?>
                  <?php if($col_name == 'codes') : ?>
					<a class="sort" href="<?php echo url_for($s_url.'&orderby=main_code_indexed'.( ($orderBy=="main_code_indexed" && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.
                    $currentPage);?>">
						<?php echo $col[1];?>
						<?php if($orderBy == $col[0]) echo $orderSign; ?>
					</a>
                  <?php else:?>
						<?php echo $col[1];?>
                  <?php endif ; ?>
				<?php endif;?>
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
				<?php if($sf_user->isAtLeast(Users::ADMIN) || $specimen->getHasEncodingRights()) : ?>
					<?php echo link_to(image_tag('edit.png', array("title" => __("Edit"))), 'specimen/edit?id='.$specimen->getId(), array('target' => '_blank'));?>&nbsp;&nbsp;
					<?php echo link_to(image_tag('duplicate.png', array("title" => __("Duplicate"))), 'specimen/new?duplicate_id='.$specimen->getId(), array('class' => 'duplicate_link'));?>&nbsp;&nbsp;
				<?php endif; ?>
				<?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))), 'specimen/view?id='.$specimen->getId(), array('target' => '_blank'));?>&nbsp;&nbsp;
              </td>
              <?php include_partial('result_content_specimen', array( 'specimen' => $specimen, 'codes' => $codes, 'is_specimen_search' => $is_specimen_search)); ?>
              <?php include_partial('result_content_individual', array( 'specimen' => $specimen, 'is_specimen_search' => $is_specimen_search)); ?>
              <?php include_partial('result_content_part', array( 'specimen' => $specimen, 'is_specimen_search' => $is_specimen_search)); ?>

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

$(document).ready(function () {
     //ftheeten 2017 02 07
    var tmpPageStr="<?php echo $currentPage; ?>";
    tmpPage=parseInt(tmpPageStr);
    var anchor=$("#criteria_butt");
    var modePage="1";
    if(tmpPage>1)
    {
        tmpPage--;
        var modePage="0";
    }
    else
    {
         var modePage="1";
    }
    $("#mode_back_button").val(modePage);
    var test_back_mode=$("#mode_back_button").val();
    var anchor=undefined;
    
    //ftheeten 2017 02 07
  
  if (window.history && window.history.pushState) {
 

    window.history.pushState('forward', null, '.');

    $(window).on('popstate', function() {

       
       
        if(test_back_mode=="0")
        {
             anchor=$("a").filter(function() {   return $(this).text() === String(tmpPage);}).first();
        }
        else
        {
            anchor=$("#criteria_butt");
        }
        
        anchor.click();
         event.stopPropagation();
    });

  }


	//rmca 2016 05 12
	var gtu_loaded=false;
	var gtu_expanded=false;

	//added by ftheeten for label printing
	if ($("#h_current_page").length){
			//alert('<?php echo $currentPage; ?>');
			$("#h_current_page").val('<?php echo $currentPage; ?>');
			//alert($("#current_page").val());
	}
	if ($("#h_order_by").length)
	{
		$("#h_order_by").val('<?php echo $orderBy; ?>');
	}
	
	if ($("#h_order_dir").length)
	{
		$("#h_order_dir").val('<?php echo $orderDir; ?>');
	}
	//end added by ftheeten
	
  //Init screen size
  check_screen_size();

  //Init resize of screen
  $(window).resize(check_screen_size);

  //Init columns visibilty
  $('ul.column_menu .col_switcher :not(:checked)').each(function(){
    $('.col_' + $(this).val()).hide();
  });

  //Init custom checkbox
  $('input[type=checkbox], input[type=radio]').not('label.custom-label input').customRadioCheck();
  
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


});
</script>
