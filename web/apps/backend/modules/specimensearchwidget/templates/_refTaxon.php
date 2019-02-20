<table>
  <thead>
    <tr>
      <td colspan="2">
        <input type="button" id='taxon_precise' value="<?php echo __('Precise search'); ?>" disabled>
        <input type="button" id='taxon_full_text' value="<?php echo __('Name search'); ?>">
      </td>
    </tr>
    <tr id="taxon_full_text_line" class="hidden">
      <th><?php echo $form['taxon_name']->renderLabel();?></th>
      <th><?php echo $form['taxon_level_ref']->renderLabel();?></th>
    </tr>
  </thead>
  <tbody>
    <tr id="taxon_full_text_line" class="hidden">
      <td><?php echo $form['taxon_name'];?></td>
      <td><?php echo $form['taxon_level_ref'];?></td>
    </tr>
    <tr id="taxon_precise_line">
      <td id="taxon_relation"><?php echo $form['taxon_relation'];?></td>
      <td style="width:400px"><select  class="ref_name" name="select2_taxa" id="select2_taxa" multiple="multiple"></select><?php echo $form['taxa_list'];?></td><td><?php echo $form['taxa_list_placeholder'];?>
      <div style="text-align:left" class="ref_name button" title="Choose Taxon" id="select2_taxa_button" ><a class='but_more' title="Choose Taxon" data-field-to-clean="taxon_name" href="<?php print(url_for('taxonomy/choose')) ?>" id="but_taxon_select2" ></a></div></td>      
    </tr>
    <tr><td></td></tr>
    <tr>
        <td><?php echo __("Taxonomic group : ");?></td>
        <td><?php echo $form['taxonomy_metadata_ref'];?></td>
    </tr>
  </tbody>
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
  });

  $('#taxon_full_text').click(function() {
    $('#taxon_precise').removeAttr('disabled') ;
    $('#taxon_full_text').attr('disabled','disabled') ;
    $('#taxon_precise_line').toggle() ;
    $(this).closest('table').find('#taxon_full_text_line').toggle() ;

  });

  if($('#specimen_search_filters_taxon_name').val() != '')
  {
    $('#taxon_full_text').trigger("click") ;
  }

  $('#taxon_relation ul.radio_list input').click(function () {
    if ( $(this).val() in { child : "child", direct_child : "direct_child" } ) {
      $('#taxon_child_syn_included').removeClass('hidden');
    }
    else {
      $('#taxon_child_syn_included').addClass('hidden');
    }
  });

  if (!($('#taxon_relation input:checked').val() in { child : "child", direct_child : "direct_child" } )) {
    $('#taxon_child_syn_included').addClass('hidden');
  }

  $('.taxon_name').on(
    'change',
    function() {
      if($(this).val() !== '') {
        $('.taxon_autocomplete').val('');
      }
    }
  );
  

  
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
                /*autocomplete_rmca_array_taxon_text=$('.col_check:checked').map(function(){
                       return $(this).parent().parent().text();
                    }).get();
              */
              var textCollections="Search in all collections";  
              if(autocomplete_rmca_array_taxon.length>0)
              {
                 //textCollections="Search in : "+ autocomplete_rmca_array_taxon_text.join(", ");
                 flagAll=false;                 
              }
              else
              {
              
                 //textCollections="Search in all collections"; 
                 flagAll=true;
              }
              
              //$("#display_searched_collections_taxon").text(textCollections);
			
 }
  
  $('.col_check').change(
        function()
        {
            initCollectionCheck();
        }
  );  
  
   //ftheeten 2018 11 22
    
       var getTaxaUrl=function()
       {
            return "<?php echo(url_for('catalogue/completeNameTaxonomyWithRef?'));?>";
       };
       
       $('#select2_taxa').select2({
				width: "75%",
                minimumInputLength : 1,
				tags: true,
				tokenSeparators: ['|'],
				  ajax: {
				    //url: getCodeUrl(),
                    transport: function (params, success, failure) {
                        var taxon_ref="";
                        if($(".col_check_metadata_ref").val().length>0)
                        {
                            taxon_ref=$(".col_check_metadata_ref").val();
                        }
                       
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
                                        taxon_ref:taxon_ref
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
                     alert("2");
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
