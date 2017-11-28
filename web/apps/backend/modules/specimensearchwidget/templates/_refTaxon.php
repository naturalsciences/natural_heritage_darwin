<div id="display_searched_collections_taxon">Search in all collections</div>
<table>
  <thead>
  <!--ftheeten  2017  06 26 level displayed in both mdoes-->
    <tr>
        
        <th style='width:60%; text-align:right'><?php echo $form['taxon_level_ref']->renderLabel();?></th>
        <td><?php echo $form['taxon_level_ref'];?></td>
    </tr>
</table>
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
   </tr>
  </thead>
  <tbody>
    <tr id="taxon_full_text_line" class="hidden">
      <td><?php echo $form['taxon_name'];?></td>
    </tr>
    <tr id="taxon_precise_line">
      <td><?php echo $form['taxon_relation'];?></td>
      <td><?php echo $form['taxon_item_ref'];?></td>
    </tr>
    </tbody>
</table>

<script type="text/javascript">
$(document).ready(function () {
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
    $('.col_check').change(
		function(i)
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

});
</script>
