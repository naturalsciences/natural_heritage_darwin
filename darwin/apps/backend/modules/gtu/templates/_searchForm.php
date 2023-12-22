<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<!--[if lte IE 8]>
    <link rel="stylesheet" href="/leaflet/leaflet.ie.css" />
<![endif]-->
<style>
.select2-dropdown.increasedzindexclass {
  z-index: 999999;
}
</style>
<div class="catalogue_gtu">
<?php echo form_tag('gtu/search'.( isset($is_choose) && $is_choose  ? '?is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'gtu_filter'));?>
  <div class="container">
    <table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
     <thead>
        <tr>
			<!--ftheeten 2016 06 28-->
			<?php if($sf_params->get('with_js') == true):?>      
				<tr>
					<td colspan="4" ><div style='float: left'> <div class='blue_link' id='get_ig'><a style='text-align: left'><?php echo __('Get IG');?></a></div>
					<div class='blue_link' id='get_specimen_number'><a><?php echo __('Get Specimen number');?></a></div>
					<div class='blue_link' id='get_station_number'><a><?php echo __('Get stations number');?></a></div></td>
				</tr>
			<?php endif;?>   
      </thead>
      <tbody>
        <!--ftheeten 2018 08 05-->               
        <tr>		  
			  <th><?php echo $form['code']->renderLabel() ?>:</th>
			  <td><?php echo $form['code']->render() ?></td>
			  </tr>
			  <tr>
			  <th><?php echo $form['ig_number']->renderLabel() ?>:</th>
			  <td><?php echo $form['ig_number']->render() ?></td>
		  </tr>
		  <tr>
			<th colspan="4"><?php echo $form['tags']->renderLabel() ?></th>
			</tr>

			<?php echo include_partial('andSearch',array('form' => $form['Tags'][0], 'row_line' => 0));?>

			<tr class="and_row">
				<td colspan="3"></td>
			<td>
            <?php echo image_tag('add_blue.png');?> <a href="<?php echo url_for('gtu/andSearch');?>" class="and_tag"><?php echo __('And'); ?></a>
          </td>
         </tr>
		  <tr>
		  <td colspan="3">
				<table style="border:solid;">
				<tr> <th ><?php print(__("People")); ?>:</th></tr>
				 <tr class="tag_button_line_people">
				  <td colspan="2">
					<input type="button" id='people_switch_precise' value="<?php echo __('Precise search'); ?>" disabled>
					<input type="button" id='people_switch_fuzzy' value="<?php echo __('Fuzzy search'); ?>">
				  </td>
				</tr>
				 <tr class="tag_header_line_people">
					<th colspan="2" class="precise_people"><?php echo $form['people_ref']->renderLabel();?></th>
					<th  colspan="2"class="fuzzy_people hidden"><?php echo $form['people_fuzzy']->renderLabel();?></th>
					<th><?php echo $form['role_ref']->renderLabel();?></th>
				 </tr>
				 <tr class="tag_content_line_people">
				  <td class="precise_people" colspan="2"><?php echo $form['people_ref'];?></td>
				  <td class="fuzzy_people hidden" colspan="2"><?php echo $form['people_fuzzy'];?></td>
				  <td><?php echo $form['role_ref'];?></td> 				  
				</tr>
				</table>
			</td>
        </tr>
		
        <!--ftheeten 2018 03 23-->
        
     
		<tr>
		  <th><?php echo $form['nagoya']->renderLabel() ?>:</th>
          <td><?php echo $form['nagoya']->render() ?> </td>
        </tr>
      </tbody>

      </table>

      <fieldset id="lat_long_set">
        <legend><?php echo __('Show Result as map');?> <input type="checkbox" id="show_as_map" autocomplete="off"></legend>
          <table>
            <tr>
              <td>
              </td>
              <th>
                <?php echo $form['lat_from']->renderLabel();?>
              </th>
              <th>
                <?php echo $form['lon_from']->renderLabel();?>
              </th>
            </tr>
            <tr>
              <th class="right_aligned"><?php echo __('Between');?></th>
              <td><?php echo $form['lat_from'];?></td>
              <td><?php echo $form['lon_from'];?><?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?></td>
            </tr>
            <tr>
              <th class="right_aligned"><?php echo __('And');?></th>
              <td><?php echo $form['lat_to'];?></td>
              <td><?php echo $form['lon_to'];?><?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?></td>
            </tr>
          </table>
          <div id="map_search_form" style="display:none">
            <?php echo __('Show accuracy of each point');?> <input type="checkbox" id="show_accuracy" /><br /><br />
            <div style="height:400px;width:600px" id="smap"></div>

 <div class="pager paging_info hidden">
   <?php echo image_tag('info2.png');?>
    <span class="inner_text"></span>
  </div>

         </div>
    </fieldset>
    <?php echo $form->renderHiddenFields();?>
    <div class="edit">
      <input class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" />
    </div>
<div class="clear"></div>
    <script  type="text/javascript">
    //ftheeten 2018 04 10
    var urlParam= function(name){
            var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
            if (results==null){
               return null;
            }
            else{
               return decodeURI(results[1]) || 0;
            }
        }  
    
    initSearchMap();

	
	
	
    $(document).ready(function () 
	{
	
		$('.select2_people').select2({
			 width: "500px",
			 multiple:true, 
			 dropdownCssClass: "increasedzindexclass",
			 ajax: {
					url: '<?php echo(url_for('catalogue/completeName'));?>',
					data: function (params) {
						//console.log(params);
					  var query = {
						table:"people",
						term: params.term,
						
					  }

					 
					  return query;
					},
						  
					processResults: function(data) {
						   var myResults = [];
							$.each(data, function (index, item) {
								myResults.push({
									'id': item.value,
									'text': item.label
								});
							});
							return {
								results: myResults
							};
						}
				},
				
		});
		


		  $('.catalogue_gtu').choose_form({});

		  $(".new_link").click( function() {
			url = $(this).find('a').attr('href'),
			data= $('.search_form').serialize(),
			reg=new RegExp("(<?php echo $form->getName() ; ?>)", "g");   
			open(url+'?'+data.replace(reg,'gtu'));
			return false;  
		  });


		  var num_fld = 1;
		  $('.and_tag').click(function()
		  {
			hideForRefresh('#gtu_filter');
			$.ajax({
				type: "GET",
				url: $(this).attr('href') + '/num/' + (num_fld++) ,
				success: function(html)
				{
				  $('table.search > tbody .and_row').before(html);
				  showAfterRefresh('#gtu_filter');
				}
			});
			return false;

		  });
		  

			
		   //ftheeten 2016 06 28
			$('#get_ig').click( function()
			{
			   var idGTU=$("#specimen_ig_ref_name").val();
				if(!!idGTU)
				{
						$("#gtu_filters_code").val(idGTU);
				}
				else
				{
					 $("#gtu_filters_code").val('');
				}
			});
			//ftheeten 2016 06 28
			$('#get_specimen_number').click( function()
			{
				var code_word="";
				if($("#specimen_newCodes_0_code_prefix").length>0)
				{
					
					code_word="#specimen_newCodes_0_code";
				}
				else if($("#specimen_Codes_0_code_prefix").length>0)
				{
						code_word="#specimen_Codes_0_code";
				}
				var valSpecCodePrefix=$(code_word+"_prefix").val()||'';
				var valSpecCodePrefixSeparator= $(code_word+"_prefix_separator").val()||'';
				var valSpecCode= $(code_word).val()||'';
				var valSpecCodeSuffixSeparator=$(code_word+"_suffix_separator").val()||"";
				var valSpecCodeSuffix= $(code_word+"_suffix").val()||'';
				var codeTotal=valSpecCodePrefix.concat(valSpecCodePrefixSeparator.concat(valSpecCode.concat(valSpecCodeSuffixSeparator.concat(valSpecCodeSuffix))));
				if(!!codeTotal)
				{
						$("#gtu_filters_code").val(codeTotal);
				}
				else
				{
					 $("#gtu_filters_code").val('');
				}
			});
			
			//ftheeten 2016 06 28
			$('#get_station_number').click( function()
			{
			   
			   if($("#staging_gtu_ref_name").length)
				{
					var gtu_value=$("#staging_gtu_ref_name").val();      
					$("#gtu_filters_code").val(gtu_value);
				}
				else
				{
				   var idGTU=$(".view_loc_code").text();
					if(!!idGTU)
					{
							$("#gtu_filters_code").val(idGTU);
					}
					else
					{
						 $("#gtu_filters_code").val('');
					}
				}
			});
			
		   //ftheeten 2018 04 10
			  var ig_num=urlParam('ig_num');
			  if(!!ig_num)
			  {
				
					$("#gtu_filters_ig_number").val(decodeURIComponent(ig_num));
					$( ".search_form" ).submit();
			  }    
        //people ctrl part
		
		$('#people_switch_precise').click(function() {

			$('#people_switch_precise').attr('disabled','disabled') ;
			$('#people_switch_fuzzy').removeAttr('disabled') ;
			$('.fuzzy_people').hide();
			$('.precise_people').show();
			$(this).closest('table').find('.people_switch_fuzzy').toggle() ;
		   
			check_state();
			// $('#specimen_search_filters_Peoples_people_ref_name').html("") ;
			// $('#specimen_search_filters_Peoples_people_ref').val("") ;
		  });

		  $('#people_switch_fuzzy').click(function() {

			$('#people_switch_precise').removeAttr('disabled') ;
			$('#people_switch_fuzzy').attr('disabled','disabled') ;
			$('.fuzzy_people').show();
			$('.precise_people').hide();
			$('.fuzzy_people').find('input:text').val("") ;
			check_state();
		  });
		  
		   if($('.class_fuzzy_people').val() != '')
		  {
			tmpVal=$('.class_fuzzy_people').val();
			$('#people_switch_fuzzy').trigger("click") ;
			$('.class_fuzzy_people').val(tmpVal);
		  }
        
    });
	
	function check_state()
  {
			
			if(($(".fuzzy_people").is(":visible")))
			{

				var valTmp=$('#specimen_search_filters_Peoples_people_ref_name').text();
				if(valTmp.length>0)
				{
					$('.fuzzy_people').find('input:text').val(valTmp);
					$('#specimen_search_filters_Peoples_people_ref_name').html("") ;
					$('#specimen_search_filters_Peoples_people_ref').val("") ;
				}
			}
			else if(($(".precise_people").is(":visible")))
			{

				$('.fuzzy_people').find('input:text').val("");
			}
  }
    </script>
    <div class="search_results">
      <div class="search_results_content"> 
      </div>
    </div>
    <div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for('gtu/new') ?>"><?php echo __('New');?></a></div>
  </div>
</form> 
</div>

