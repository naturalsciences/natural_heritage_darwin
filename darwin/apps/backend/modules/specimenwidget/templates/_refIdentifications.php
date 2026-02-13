<script  type="text/javascript">


function forceIdentifiersHelper(e,ui)
{
   $(".ui-state-highlight").html("<td colspan='3' style='line-height:"+ui.item[0].offsetHeight+"px'>&nbsp;</td>");
}


function reOrderIdentifiers(tableId)
{
  $('table#'+tableId).find('tbody.spec_ident_identifiers_data:visible').each(function (index, item){
    $(item).find('tr.spec_ident_identifiers_data input[id$=\"_order_by\"]').val(index+1);
  });
}

function addIdentifierValue(people_ref,ref_table)
{
  targetUrl = $(ref_table+' tfoot div.add_code a.hidden').attr('href');
  $.ajax(
  {
    type: "GET",
    url: targetUrl+ (0+$(ref_table+' tbody').length)+'/people_ref/'+people_ref + '/iorder_by/' + ($(ref_table+' .spec_ident_identifiers_data:visible').length+1),
    success: function(html)
    {
      $(ref_table).append(html);
      $(ref_table).find('thead:hidden').show();
      $(ref_table).toggleClass('green_border',true);
    }
  });
  return false;
}
</script>
<div>
<div id="identification_placeholder" style="visibility: hidden"></div>
<table class="property_values" id="identifications" >
  <thead style="<?php echo ($form['Identifications']->count() || $form['newIdentification']->count())?'':'display: none;';?>" class="spec_ident_head">
    <tr>
      <th><?php echo $form['ident'];?></th>
      <th><?php echo __('Date'); ?></th>
      <th><?php echo __('Category');?></th>
      <th><?php echo __('Subject'); ?></th>
      
      
    </tr>
  </thead>   
    <?php $retainedKey = 0;?>
    <?php foreach($form['Identifications'] as $form_value):?>
	 
      <?php include_partial('specimen/spec_identifications', array('form' => $form_value, 'row_num'=>$retainedKey, 'module'=>$module, 'spec_id'=>$spec_id, 'individual_id'=>$individual_id, 'identification_id'=> $retainedKey));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>
    <?php foreach($form['newIdentification'] as $form_value):?>
		
      <?php include_partial('specimen/spec_identifications', array('form' => $form_value, 'row_num'=>$retainedKey, 'module'=>$module, 'spec_id'=>$spec_id, 'individual_id'=>$individual_id));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>
  <tfoot>
    <tr>
      <td colspan='6'>
        <div class="add_code">
          <a href="<?php echo url_for($module.'/addIdentification'. (($spec_id == 0) ? '': '?spec_id='.$spec_id.(($individual_id == 0) ? '': '&individual_id='.$individual_id)));?>/num/" id="add_identification"><?php echo __('Add identification');?></a>
        </div>
      </td>
    </tr>
  </tfoot>
</table>
</div>
<?php echo javascript_include_tag('catalogue_people.js') ?>
<script  type="text/javascript">

$(document).ready(function () {
	
	
	
	$("body").on("click",".clear_identification",
		function()
		{
			
			var id_row=$(this).attr("id_row");
			var name_ctrls="specimen[newIdentification]["+id_row.toString()+"]";
			
			parent_el = $(this).closest('tbody');

			  $(parent_el).find('input[id$=\"_value_defined\"]').val('');
			  $(parent_el).find('input[id$=\"_is_removed\"]').val('');

			  $(parent_el).find('select').append("<option value=''></option>").val('');
				//$(parent_el).html("");
			  $(parent_el).hide();
			  $(parent_el).remove();
			  //reOrderIdent();
			  reOrderIdentifiers("spec_ident_data_"+id_row)
			  visibles = $('table#identifications tbody.spec_ident_data:visible').size();
			  if(!visibles)
			  {
				$(this).closest('table#identifications').find('thead.spec_ident_head').hide();
				$(this).closest('table#identifications').find('thead.spec_ident_head').attr("visibility", "hidden");
				
			  }
			  
			  $("#spec_ident_data_"+id_row).hide();
			  var to_remove=$('[name^="'+name_ctrls+'"]' );
			  to_remove.remove();
			 
			
		}
	);
	
	$("body").on("click",".show_hide_ident_count",
		function()
		{
			
			var id_row=$(this).attr("id_row");
			var list_rows =$("tr.toggle_count_ident[id_row='"+id_row+"']");	
			list_rows.toggle();
			
		}
	);
	
	$("body").on("keypress", ".identification_subject", 
		function()
		{
			console.log("click");
			$(this).autocomplete({
				  minLength: 3,
				  source: function( request, response ) {
					$.getJSON('<?php echo url_for('catalogue/completeName?table=taxonomy');?>', {term : request.term }, function( data) {
						response( $.map( data, function( item ) {
						  return {
							label: item.label,
							value: item.label
						  }
						}));

					});
				  }
				});
		}
	
	)
	
	<?php if(!$form->getObject()->isNew()): ?> 
		console.log("EDIT");
		
		function init_ident_count()
		{
			console.log("init_ident_count");
		}
		
		init_ident_count();
	<?php endif; ?> 

    $('#add_identification').click(function()
    {
        hideForRefresh('#refIdentifications');
        parent_el = $(this).closest('table#identifications');
        $.ajax(
        {
          type: "GET",
          url: $(this).attr('href')+ ($('tbody.spec_ident_data').length) + '/order_by/' + ($('tbody.spec_ident_data:visible').length+1),
          success: function(html)
          {
            $(parent_el).append(html);
            $(parent_el).find('thead.spec_ident_head:hidden').show();
            showAfterRefresh('#refIdentifications');
          }
        });
        return false;
    });    

       $(".set_accuracy").on("change",
			function()
			{
				console.log("change");
				var id=$(this).attr("id");
				if(id!==undefined)
				{
					console.log(id);
					const regex = /\d+/g;

					const found = id.match(regex);
					if(found.length>0)
					{
						idx=found[0];
						console.log(idx);
						if(idx.indexOf("new")!=-1)
						{
							is_new=true;
						}
						else
						{
							is_new=false;
						}
						console.log("change_accuracy");
						showHideCount_gen_ident($(this), "", idx ,is_new)
					}
				
				}
			}
	   );
	   


    });
    
    //ftheeten 2018 09 18

	function showHideCount_gen_ident(acc_fld, param, index, is_new) {
		console.log("switch");
		if(is_new)
		{
			new_prefix="new"
		}
		else
		{
			new_prefix=""
		}
		//var $acc_fld = $('#specimen_accuracy_'+param+'0');
		var $min_fld = $('#specimen_'+new_prefix+'Identifications_'+index+'_'+param+'min');
		var $max_fld = $('#specimen_'+new_prefix+'Identifications_'+index+'_'+param+'max');
		console.log($min_fld);
		console.log($max_fld);
		// precise
		if(acc_fld.is(':checked')) {
		  $max_fld.closest('tr').hide();
		  $max_fld.val( $min_fld.val());
		  //ftheeten 2018 02 05
		  $("[for="+$min_fld.attr('id')+"]").text("Value");
		}else {
		  $max_fld.closest('tr').show();
		  //ftheeten 2018 02 05
		  if(param =="")
		  {		 
			$("[for="+$min_fld.attr('id')+"]").text("Min.");
		  }
		  if(param=='males_'||param=='females_'||param=='juveniles_')
		  {
			$('#specimen_accuracy_1').click();
			//ftheeten 2018 02 05
			$("[for="+$min_fld.attr('id')+"]").text("Min.");
			showHideCount_gen('');
			syncCounters('_max');
		  }
		}
  }
        onElementInserted('body', '.identification_subject', function(element)
        {
            $(element).val($("#specimen_taxon_ref_name").val());
        }
        );
</script>



