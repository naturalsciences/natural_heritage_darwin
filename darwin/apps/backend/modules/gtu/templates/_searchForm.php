<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php
	$flagMenu=detect_menu_hidden();
?>
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
        
          <th><?php echo $form['code']->renderLabel() ?></th>
          <th><?php echo $form['gtu_from_date']->renderLabel(); ?></th>
          <th><?php echo $form['gtu_to_date']->renderLabel(); ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>		 
          <td><?php echo $form['code']->render() ?></td>
          <td><?php echo $form['gtu_from_date']->render() ?></td>
          <td><?php echo $form['gtu_to_date']->render() ?></td>
          <td></td>
        </tr>
        <tr>
          <th colspan="4"><?php echo $form['tags']->renderLabel() ?></th>
        </tr>

        <?php echo include_partial('andSearch',array('form' => $form['Tags'][0], 'row_line' => 0));?>

        <tr class="and_row">
          <td colspan="3"></td>
          <td>
             <a href="<?php echo url_for('gtu/andSearch');?>" class="and_tag"><?php echo image_tag('add_blue.png');?></a><?php print($form['tag_boolean']->render()); ?>
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
        <!--ftheeten 2018 08 08-->
        <tr>
            <th><?php echo $form['ig_number']->renderLabel() ?></th>
		  <th><?php echo $form['expedition']->renderLabel() ?></th>
           <th><?php echo __("Technical ID") ?></th>
        </tr>
        
        <td><?php echo $form['ig_number']->render() ?></td>
        <td><?php echo $form['expedition']->render() ?></td>
         <td><?php echo $form['id']->render() ?></td>
         <td><input type="button" id="last_encoded" name="last_encoded" value="<?php print(__("Last encoded")); ?>"</input> </td>
        </tr>
        <!--ftheeten 2018 08 08-->
        <tr>
		  <th><?php echo $form['collection_ref']->renderLabel() ?></th>
		  <th></th>
		   <th><?php echo $form['import_ref']->renderLabel() ?></th>
        </tr>
        <tr>
        <td><?php echo $form['collection_ref']->render() ?> All :<input type="checkbox" id="all_collections" class="all_collections" checked></td>
        <td></td>
		<td><?php echo $form['import_ref']->render() ?></td>
		</tr>
      </tbody>

      </table>

      <fieldset id="lat_long_set">
        <legend><?php echo __('Show Result as map');?>
        <!--ftheeten 2018 09 28-->
        <?php if(strpos($_SERVER["REQUEST_URI"], "with_js")):?>
               <input type="checkbox" id="show_as_map" autocomplete="off">
        <?php else:?>
            <input type="checkbox" id="show_as_map" autocomplete="off" checked>
         <?php endif;?>
        </legend>
        <!--ftheeten 2018 09 28-->
        <?php if(strpos($_SERVER["REQUEST_URI"], "with_js")):?>        
            <table >
          <?php else:?>
            <table style="display:none">
          <?php endif;?>
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
           <!--ftheeten 2018 09 28-->
            <?php if(strpos($_SERVER["REQUEST_URI"], "with_js")):?>
                <div id="map_search_form" style="display:none">
            <?php else:?>
                <div id="map_search_form" >
            <?php endif;?>
            <?php echo __('Show accuracy of each point');?> <input type="checkbox" id="show_accuracy" /><br /><br />
            <div style="height:400px;width:100%" id="smap"></div>

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
      <script type="text/javascript">
        initSearchMap();

        $(document).ready(function () {
		
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
			
				
		$(".but_more").click(
			function()
			{
				if($('#all_collections:checked').length>0)
				{
					$("#all_collections").click();
				}
			}
		);
		
		

        
       $("#all_collections").change(
            function()
            {
                if(this.checked)
                {
                    oldCollId=$(".collection_ref").val();
                    $(".collection_ref").prop('disabled', true);
                    $(".collection_ref").val("/");
                }
                else
                {
                    $(".collection_ref").prop('disabled', false);
                    $(".collection_ref").val(oldCollId);
                }
            }
       
       );
       // onload
        $('#all_collections').prop('checked', true);
        $(".collection_ref").prop('disabled', true);
        $(".collection_ref").val("/");
        
          $('.catalogue_gtu').choose_form({});

          $(".new_link").click( function() {
            url = $(this).find('a').attr('href');			
			data= $(".gtu_code_callback[value!='']").serialize();			
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
		  
		  //ftheeten 2019 01 29
		 <?php if(array_key_exists("name", $_REQUEST)): ?>
				if($(".gtu_code_callback").length)
				{
					
					$(".gtu_code_callback").val("<?php print($_REQUEST["name"]);?>");
				}
			   <?php endif;?>
               
           $("#last_encoded").click(
            function()
            {
                var url_last="<?php echo(url_for('gtu/getLastEncodedId?'));?>";
                 $.getJSON(url_last, {                                
                            } , 
                            function (data) 
                            {
                                $(".gtu_id_callback").val(data.id);
                                $(".search_submit").click();
                                //onElementInserted("body", ".result_choose", function(){$(".result_choose").click();} )
                            });
                   
            }
           );  

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
        
                 

            
          //ftheeten 2018 03 08
          var url="<?php echo(url_for('catalogue/expeditionsAutocomplete?'));?>";
          var autocomplete_rmca_array=Array();
          $('.autocomplete_for_expeditions').autocomplete({
                source: function (request, response) {
                    $.getJSON(url, {
                                term : request.term
                            } , 
                            function (data) 
                                {
                            response($.map(data, function (value, key) {
                            return value;
                            }));
                    });
                },
                minLength: 2,
                delay: 200
            });
			


      </script>
      <div class="search_results">
        <div class="search_results_content"></div>
      </div>
      <?php if($flagMenu):?><div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for('gtu/new') ?>"><?php echo __('New');?></a></div><?php endif;?>
  </div>
</form> 
</div>
