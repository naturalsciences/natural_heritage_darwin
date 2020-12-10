<table>
  <thead>
  <!--ftheeten  2017  06 26 level displayed in both mdoes-->
    <tr>
        <th style='width:25%; text-align:left'>
			<?php echo $form['taxon_level_ref']->renderLabel();?>:
		</th>
        <td style='width:75%; text-align:left'>
			<?php echo $form['taxon_level_ref'];?>
		</td>
    </tr>
    <tr>
      <td colspan="2">
        <input type="button" id='taxon_precise' value="<?php echo __('Precise search'); ?>" disabled>
        <input type="button" id='taxon_full_text' value="<?php echo __('Name search'); ?>">
		 (<div id="display_searched_collections_taxon" style="display: inline-block;text-align:left">Search in all collections</div>)
      </td>
    </tr>
    <tr id="taxon_full_text_line" class="hidden">
      <th><?php echo $form['taxon_name']->renderLabel();?></th>
      <td><?php echo $form['taxon_name'];?></td>
    </tr>
    <tr id="taxon_precise_line">
		<td><?php echo $form['taxon_relation'];?></td>
		 <!-- <td><?php echo $form['taxon_item_ref'];?></td>-->
	   <td style="width:400px">
			<select  class="ref_name" name="select2_taxa" id="select2_taxa" multiple="multiple"></select>
			<?php echo $form['taxa_list'];?>
		</td>
		<td>
			<?php echo $form['taxa_list_placeholder'];?>
			<div style="text-align:left" class="ref_name button" title="Choose Taxon" id="select2_taxa_button" >
				<a class='but_more' title="Choose Taxon" data-field-to-clean="taxon_name" href="<?php print(url_for('taxonomy/choose')) ?>" id="but_taxon_select2" ></a>
			</div>
		</td>
    </tr>

    <!-- <tr>
        <th><?php echo __("Taxonomy : ");?></th>
        <td>< ?php echo $form['taxonomy_metadata_ref'];?></td>
    </tr>-->

	<tr>
        <th><?php echo __("CITES : ");?></th>
        <td><?php echo $form['taxonomy_cites'];?></td>
    </tr>
	<tr>
        <th><?php echo __("Determination status : ");?></th>
        <td><?php echo $form['determination_status'];?></td>
    </tr>
</table>

<script type="text/javascript">
$(document).ready(function () {
  //ftheeten 2017 12 14
  $('#taxon_precise_line :checkbox').each(
    function()
    {
           $(this).prop('checked', true);
    }
    );
  $('#taxon_precise').click(function() {
    $('#taxon_precise').attr('disabled','disabled') ;
    $('#taxon_full_text').removeAttr('disabled') ;
    $('#taxon_precise_line').toggle() ;
    $(this).closest('table').find('#taxon_full_text_line').toggle() ;
    $('#taxon_full_text_line').find('input:text').val("") ;
    $('#taxon_full_text_line').find('select').val('') ;
  });

  $('#taxon_full_text').click(function() {
    $('#taxon_precise').removeAttr('disabled') ;
    $('#taxon_full_text').attr('disabled','disabled') ;
    $('#taxon_precise_line').toggle() ;
    $(this).closest('table').find('#taxon_full_text_line').toggle() ;
    $('#taxon_full_text_line').find('input:text').val("") ;
    $('#taxon_full_text_line').find('input:hidden').val('') ;

  });

  if($('#specimen_search_filters_taxon_name').val() != '')
  {
       $('#taxon_full_text').trigger("click") ;
  }
  
  //ftheeten 2017 06 26
  var autocomplete_rmca_array_taxon=Array();
  var autocomplete_rmca_array_taxon_text=Array();
  var flagAll=true;
  
    //ftheeten 2015 06 08
  //autocomplete for codes number
  
  function initCollectionCheck()
 {
				autocomplete_rmca_array_taxon=$('.col_check:checked').map(function(){
                    return $(this).val();
                    }).get();
                autocomplete_rmca_array_taxon_text=$('.col_check:checked').map(function(){
                       return $(this).parent().parent().text();
                    }).get();
              
              var textCollections="Search in all collections";  
              if(autocomplete_rmca_array_taxon.length>0)
              {
                 textCollections="Search in : "+ autocomplete_rmca_array_taxon_text.join(", ");
                 flagAll=false;                 
              }
              else
              {
              
                 textCollections="Search in all collections"; 
                 flagAll=true;
              }
              
              $("#display_searched_collections_taxon").text(textCollections);
			
 }
  
  $('.col_check').change(
        function()
        {
            initCollectionCheck();
        }
  );  

  
  var url="<?php echo(url_for('catalogue/completeName?'));?>";
 
  
	  $('#specimen_search_filters_taxon_item_ref_name').autocomplete({
		source: function (request, response) {
            var array_params={};
            if($('.taxon_level_ref').val().length>0)
            {
                array_params['level']=$('.taxon_level_ref').val();
            }
            if(autocomplete_rmca_array_taxon.length>0)
            {
                array_params['collections']=autocomplete_rmca_array_taxon.join();
            }
            array_params['term']= request.term;
            array_params['table']= 'taxonomy';
			$.getJSON(url, array_params , 
					function (data) 
						{
					response($.map(data, function (value, key) {
					return value;
                    }));
			});
		},
		minLength: 1,
		delay: 100
	});
    
    //init select level on species
    //$('.taxon_level_ref>option:eq(48)').attr('selected', true);

    //ftheeten 2018 08 03
    /*$('#specimen_search_filters_taxon_item_ref_name').click(
        function ()
        {
            $(this).parent(".complete_ref").find(".but_more").click();
           // alert($(this).closest(".but_more").attr("title"));
        }
    );*/
    
    
    //ftheeten 2018 11 22
    
       var getTaxaUrl=function()
       {
            return "<?php echo(url_for('catalogue/completeName?'));?>";
       };
       
       $('#select2_taxa').select2({
				width: "75%",
                minimumInputLength : 1,
				tags: true,
				tokenSeparators: ['|'],
				  ajax: {
				    //url: getCodeUrl(),
                    transport: function (params, success, failure) {                        
                       
                        if(params.data.term.length>=3)
                        {
                            var $request= $.ajax(
                                 {
                                  dataType: "json",
                                  url:  getTaxaUrl(),
                                  data: {
                                        term : params.data.term,
                                        collections: autocomplete_rmca_array.join(),
                                        table: 'taxonomy',
                                  }
                                }
                            );
                        }
                        else
                        {
                             var $request= $.ajax(
                                 {
                                  dataType: "json",
                                  url:  getTaxaUrl()+'&table=taxonomy',                                  
                                }
                            );
                        }

                        $request.then(success);
                        $request.fail(failure);

                        return $request;
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
				  }
				});
       
       
       
      $("form").submit(function (e) {
                var criteria=new Array();
                var values=new Array();
                $.each(
                    $('#select2_taxa').select2("data"),
                    function(index, data)
                    {
                       
                        criteria.push(data.id);
                        values.push(data.text);
                    }
                );               
                $(".select2_taxa_values").val(criteria.join(";"));
                
                $(".select2_taxa_list_placeholder").val(values.join("|"));
                
                
            });
            
            
                  //back 
        <?php if( array_key_exists("specimen_search_filters", $_POST)):?>
            <?php $tmpPOST=$_POST["specimen_search_filters"]; ?>
             <?php if( array_key_exists("taxa_list", $tmpPOST)):?>               
                <?php $listValues=explode(";",$tmpPOST['taxa_list']);?>
                    <?php if( array_key_exists("taxa_list_placeholder", $tmpPOST)):?>                  
                        <?php $listText=explode("|",$tmpPOST['taxa_list_placeholder']);?>
                    <?php else:?>

                        <?php $listText=$listValues;?>
                    <?php endif;?>
                    <?php $i=0;?>
                    <?php foreach( $listValues as $val):?>
                        var valueToCopy="<?php print($val); ?>";
                        var textToCopy="<?php print($listText[$i]); ?>";
                        if(valueToCopy.trim().length>0)
                        {                     
                            select2SetOption('#select2_taxa', valueToCopy, textToCopy);
                        }
                        <?php $i++;?>
                <?php endforeach;?>
             <?php endif;?>
             <?php if( array_key_exists("exact_codes_list", $tmpPOST)):?>
                <?php if(strtolower($tmpPOST["exact_codes_list"])=="on"):?>
                    $("#specimen_search_filters_exact_codes_list").prop("checked",true);
                <?php else:?>
                    $("#specimen_search_filters_exact_codes_list").prop("checked",false);
                <?php endif;?>
            <?php endif;?>        
        <?php endif;?>
            
       initCollectionCheck();
       
        $("#but_taxon_select2").click(button_ref_modal);

        
		 $( "body" ).on( "close_modal", function(event, api) { 
			if($("#ui-tooltip-modal").html().toLowerCase().indexOf("taxon") >= 0)
			{
				select2SetOption('#select2_taxa', ref_element_id, ref_element_name);
				$("#ui-tooltip-modal").qtip('destroy', true);           
				event.preventDefault();
				event.stopImmediatePropagation();
			}
			
   
        });
		
		 
  });
</script>
