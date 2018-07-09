<tr class="<?php echo $type;?>_data" id="<?php echo $type;?>_<?php echo $row_num; ?>">
    <td><?php echo image_tag('drag.png','class='.$type.'_table_handle');?></td>
    <td>
        <?php echo image_tag('info-green.png',"title=info class=".$type."_extd_info_$row_num");?>
        <div class="extended_info" style="display:none;">          
        </div>
        <script  type="text/javascript">
          $(".<?php echo $type;?>_extd_info_<?php echo $row_num;?>").qtip({
            show: { solo: true, event:'mouseover' },
            hide: { event:'mouseout' },
            style: 'ui-tooltip-light ui-tooltip-rounded ui-tooltip-dialogue',
            content: {
              text: '<img src="/images/loader.gif" alt="loading"> Loading ...',
              title: { text: '<?php echo __("People Info") ; ?>' },
              ajax: {
                url: '<?php echo url_for("people/extendedInfo");?>',
                type: 'GET',
                data: { id: '<?php echo $form["people_ref"]->getValue() ; ?>' }
              }
            }
          });        
        </script>
    </td>
	<!--JMHerpers 2018 03 26
    <!--<td>
		<?php if($form['people_ref']->getValue()) : ?>
			<?php echo image_tag(Doctrine::getTable('People')->find($form['people_ref']->getValue())->getCorrespondingImage()) ; ?>
        <?php endif ; ?>
    </td>-->
    <td>
		<?php
			$is_physical = Doctrine::getTable('People')->find($form['people_ref']->getValue())->getIsPhysical();
			echo link_to($form['people_ref']->renderLabel(),
						 (($is_physical)?'people':'institution').'/edit',
						 array(
						   'query_string' => 'id='.$form['people_ref']->getValue(),
						   //ftheeten 2016 11 23
						   'target' => '_blank'
						 )
        );?>
	</td>
	<?php echo $form['people_sub_type']->render() ; ?>
    <td class="widget_row_delete">
        <?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_'.$type.'_'.$row_num); ?>
        <?php echo $form->renderHiddenFields();?>
		
		<script type="text/javascript">
		  $(document).ready(function () {
			   
			$("#clear_<?php echo $type;?>_<?php echo $row_num;?>").click( function()
			{
			   parent_el = $(this).closest('tr');
			   parentTableId = $(parent_el).closest('table').attr('id')
			   $(parent_el).find('input[id$=\"_people_ref\"]').val('');
			   $(parent_el).hide();
			   $.fn.catalogue_people.reorder( $(parent_el).closest('table') );
			   visibles = $('table#'+parentTableId+' .<?php echo $type;?>_data:visible').size();
			   
			   //JMHerpers 2018 03 30
			   $("#loans_institution_receiver option[value='']").attr('selected', true)
			   $('#loans_institution_receiver_input').val(0);
			   $('#loans_address_receiver').val("");
			   $('#loans_zip_receiver').val("");
			   $('#loans_city_receiver').val("");
			   $("#loans_country_receiver option[value='']").attr('selected', true)
			   $('#loans_country_receiver_input').val("");
			});
			$('table .hidden_record').each(function() {
			  $(this).closest('tr').hide() ;
			});
			
			//JMHerpers 2018 03 28
			
			var typefrom = '<?php echo $type;?>';
			var rowfrom = '<?php echo $row_num;?>';
			$("#loans_newActorsSender_"+rowfrom+"_people_sub_type_4").prop('checked', true);
			$("#loans_newActorsReceiver_"+rowfrom+"_people_sub_type_4").prop('checked', true);
			if (typefrom == 'receiver' & $("#loans_address_receiver").val() == "" ){
				var getAddressJson=function(){
					return JSON.parse('<?php echo Doctrine::getTable('People')->find($form['people_ref']->getValue())->getCorrespondingInstitutionandAddress() ?>');
				}
				var json_address = getAddressJson();

				if (json_address[0] != null){
					$("#loans_institution_receiver").val(json_address[0].id_instit);
					$("#loans_address_receiver").val(json_address[0].entry);
					$("#loans_zip_receiver").val(json_address[0].zip);
					$("#loans_city_receiver").val(json_address[0].locality);
					$("#loans_country_receiver_input").val(json_address[0].country);
				}
			}
		  });
		</script>
    </td>
</tr>
