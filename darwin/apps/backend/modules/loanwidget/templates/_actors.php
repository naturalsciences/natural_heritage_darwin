<table id="sender_table" class="catalogue_table edition">
  <thead>
    <tr>
		<!--JMHerpers 2018 03 23-->
      <td colspan="11">
		<!--<div> <?php echo __("Sender side") ; ?></div>  -->
		<a class="add_actor button_loan" href="<?php echo url_for('people/searchBoth?with_js=1');?>"><?php echo __('Add Sender...');?></a>
	  </td>
      <!--<th colspan="8"><?php echo __("Roles") ; ?></th>-->
    </tr>
	<tr> <td colspan="11"> </td></tr>
    <tr class="title_sender" style="visibility: hidden">
      <th colspan="3"><?php echo __("Sender") ; ?></th>      
      <th><?php echo __("Responsible") ; ?></th>
      <th><?php echo __("Contact") ; ?></th>
      <th><?php echo __("Checker") ; ?></th>
      <th><?php echo __("Preparator") ; ?></th>
      <th><?php echo __("Attendant") ; ?></th>
      <th><?php echo __("Transporter") ; ?></th>
      <th><?php echo __("Other") ; ?></th>
      <th><?php echo $form['sender'];?></th>
    </tr>
  </thead>
 <tbody id="sender_body">
   <?php $retainedKey = 0;?>
   <?php foreach($form['ActorsSender'] as $form_value):?>   
     <?php include_partial('loan/actors_association', array('type' => 'sender','form' => $form_value, 'row_num'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
   <?php endforeach;?>
   <?php foreach($form['newActorsSender'] as $form_value):?>
     <?php include_partial('loan/actors_association', array('type' => 'sender','form' => $form_value, 'row_num'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
   <?php endforeach;?>
 </tbody>
 <tfoot>
   <tr>
     <td colspan="12">
        <a href="<?php echo url_for('loan/addActors?table='.$table.($form->getObject()->isNew() ? '': '&id='.$form->getObject()->getId()) );?>/type/sender/num/" class="hidden"></a>
		<!--JMHerpers 2018 03 23-->
     </td>
   </tr>
 </tfoot>  
</table>

<table id="receiver_table" class="catalogue_table edition">
  <thead>
    <tr>
	  <!--JMHerpers 2018 03 23-->
	  <td colspan="11">

		  <a id="add_receiver" class="add_actor button_loan" href="<?php echo url_for('people/searchBoth?with_js=1');?>"><?php echo __('Add Receiver');?></a>
          </br></br>
         <a id="add_transporter" class="add_actor button_loan" href="<?php echo url_for('people/searchBoth?with_js=1');?>"><?php echo __('Add Transporter');?></a>
	  </td>
    </tr>
    <tr> <td colspan="11"> </td></tr>
    <tr class="title_receiver" style="visibility: hidden">
      <th colspan="3"><?php echo __("Receiver") ; ?></th>
      <th><?php echo __("Responsible") ; ?></th>
      <th><?php echo __("Contact") ; ?></th>
      <th><?php echo __("Checker") ; ?></th>
      <th><?php echo __("Preparator") ; ?></th>
      <th><?php echo __("Attendant") ; ?></th>
      <th><?php echo __("Transporter") ; ?></th>
      <th><?php echo __("Other") ; ?></th>
      <th><?php echo $form['receiver'];?></th>
    </tr>
  </thead>
 <tbody id="receiver_body">
   <?php $retainedKey = 0;?>
   <?php foreach($form['ActorsReceiver'] as $form_value):?>   
     <?php include_partial('loan/actors_association', array('type' => 'receiver','form' => $form_value, 'row_num'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
   <?php endforeach;?>
   <?php foreach($form['newActorsReceiver'] as $form_value):?>
     <?php include_partial('loan/actors_association', array('type' => 'receiver','form' => $form_value, 'row_num'=>$retainedKey));?>
     <?php $retainedKey = $retainedKey+1;?>
   <?php endforeach;?>
 </tbody> 
 <tfoot>
   <tr>
     <td colspan="11">
         <a href="<?php echo url_for('loan/addActors?table='.$table.($form->getObject()->isNew() ? '': '&id='.$form->getObject()->getId()) );?>/type/receiver/num/" class="hidden"></a>

     </td>
   </tr>
 </tfoot> 
</table>
<!--JMHerpers 2018 03 23-->
<table id="receiver_address_table" style="display: none">
   <tr>
	  <th colspan="8"></th>
   </tr>
    <tr>
	  <th colspan="8"><?php echo 'Receiver mailing info:' ?></th>
   </tr>
    <tr>
      <th><?php echo 'Institution' ?></th>
	  <td width='15%'><?php echo $form['institution_receiver']->renderError(); echo $form['institution_receiver'] ?></td>
	  <th colspan="6"></th>
   </tr>
   <tr>
	  <th><?php echo 'Address' ?></th>
	  <td><?php echo $form['address_receiver']->renderError(); echo $form['address_receiver'] ?></td>
	  <th><?php echo 'Zip' ?></th>
	  <td><?php echo $form['zip_receiver']->renderError(); echo $form['zip_receiver'] ?></td>
	  <th><?php echo 'City' ?></th>
	  <td><?php echo $form['city_receiver']->renderError(); echo $form['city_receiver'] ?></td>
	  <th><?php echo 'Country' ?></th>
	  <td><?php echo $form['country_receiver']->renderError(); echo $form['country_receiver'] ?></td>
   </tr>
</table>

<script  type="text/javascript">
    // ftheeten 2018 12 20
    var type_receiver="receiver";
	//JMHerpers 2018 03 21
	function get_inst_address(instid){	
		if (instid != 0 ){
			var url = "<?php echo(url_for('catalogue/Institutionaddressjson?'));?>?id="+instid;		
			$.ajax(url,
				{dataType:"json"}
			).done(
					function(data)
					{
						if (data != null){
							$("#loans_address_receiver").val(data.entry);
							$("#loans_zip_receiver").val(data.zip_code);
							$("#loans_city_receiver").val(data.locality);
							$("#loans_country_receiver_input").val(data.country);
							$("#loans_country_receiver option[value='"+data.country+"']").attr('selected', true)
						}		
						else{
							$("#loans_address_receiver").val("");
							$("#loans_zip_receiver").val("");
							$("#loans_city_receiver").val("");
							$("#loans_country_receiver_input").val("");
							$("#loans_country_receiver option[value='']").attr('selected', true)
						}
					}
				);	
		}
	}
		
	  $(document).on('change','select[name = "loans[institution_receiver]"]',function(){
		    parent_id = 'loans_country_receiver_parent';
			el = $('#'+parent_id +' select');
			el.addClass('hidden');
			$('#'+parent_id +' input').attr('name', el.attr('name'))
			$('#'+parent_id +' input').removeClass('hidden');
			el.removeAttr('name');
			$('#'+parent_id +' .change_item_button').removeClass('hidden');
			$('#'+parent_id +' .add_item_button').addClass('hidden');
	  
			instval = $("select[name = 'loans[institution_receiver]']").val();
			var pos = instval.indexOf("§§§"); 
			instval2 = instval.substring(pos+3,instval.length); 
			get_inst_address(parseInt(instval2.trim()));
          });

		  
	$(document).ready(function () {
		//JMHerpers 2018 03 30
		if($('#sender_0').length){
			$(".title_sender").css("visibility", "visible");
			$(".title_receiver").css("visibility", "visible");
			$("#receiver_address_table").css("display", "block");
		}
				
		function addSender(people_ref, people_name)
		{ //JMHerpers 2018 03 30
		  $(".title_sender").css("visibility", "visible");
		  info = 'ok';
		  $('#sender_body tr').each(function() {
			if($(this).find('input[id$=\"_people_ref\"]').val() == people_ref) info = 'bad' ;
		  });
		  if(info != 'ok') return false;
		  hideForRefresh($('.ui-tooltip-content .page')) ; 
		  $.ajax(
		  {
			type: "GET",
			url: $('#sender_table a.hidden').attr('href')+ (0+$('#sender_body tr').length)+'/people_ref/'+people_ref + '/order_by/' + (0+$('#sender_table tr').length),
			success: function(html)
			{
			  $('#sender_body').append(html);
			  $.fn.catalogue_people.reorder($('#sender_table'));
			  showAfterRefresh($('.ui-tooltip-content .page')) ; 
			}
		  }); 
		   $('body').trigger('close_modal');
		  return true;
		}

        //ftheeten 2018 12 20
        
         function addReceiver(people_ref, people_name)
        {
             type_receiver="receiver";
            addReceiver_core(people_ref, people_name);
            
        }
        
        function addTransporter(people_ref, people_name)
        {
             type_receiver="transporter";
            addReceiver_core(people_ref, people_name);
            
        }
		function addReceiver_core(people_ref, people_name)        
		{ //JMHerpers 2018 03 30
          console.log(type_receiver);
		  $(".title_receiver").css("visibility", "visible");
		  $("#receiver_address_table").css("display", "block");
		  parent_id = 'loans_country_receiver_parent';
		  el = $('#'+parent_id +' select');
		  el.addClass('hidden');
		  $('#'+parent_id +' input').attr('name', el.attr('name'))
		  $('#'+parent_id +' input').removeClass('hidden');
		  el.removeAttr('name');
		  $('#'+parent_id +' .change_item_button').removeClass('hidden');
		  $('#'+parent_id +' .add_item_button').addClass('hidden');
		  //end JMHerpers 2018 03 30
		  
		  info = 'ok';
		  $('#receiver_body tr').each(function() {
			if($(this).find('input[id$=\"_people_ref\"]').val() == people_ref) info = 'bad' ;
		  });
		  if(info != 'ok') return false;
		  hideForRefresh($('.ui-tooltip-content .page')) ; 
		  $.ajax(
		  {
			type: "GET",
			url: $('#receiver_table a.hidden').attr('href')+ (0+$('#receiver_body tr').length)+'/people_ref/'+people_ref + '/order_by/' + (0+$('#sender_table tr').length),
			success: function(html)
			{
              
			  $('#receiver_body').append(html);
              //ftheeten 2012 12 20
               console.log($(".receiver_data").last());
               //check transporter (value 64) and uncheck others
               $(".receiver_data").last().find("input[type='checkbox']").prop('checked', false);
               $(".receiver_data").last().find("input[type='checkbox'][value='64']").prop('checked', true);
			  $.fn.catalogue_people.reorder($('#receiver_table'));
			  showAfterRefresh($('.ui-tooltip-content .page')) ;  
			}
		  }); 
            $('body').trigger('close_modal');
		  return true;
		}
		$("#sender_table").catalogue_people({handle: '.sender_table_handle', add_button: '#sender_table a.add_actor', q_tip_text: '<?php echo __('Add Sender');?>',update_row_fct: addSender });
		$("#receiver_table").catalogue_people({handle: '.receiver_table_handle', add_button: '#receiver_table #add_receiver', q_tip_text: '<?php echo __('Add Receiver');?>',update_row_fct: addReceiver });
        $("#receiver_table").catalogue_people({handle: '.receiver_table_handle', add_button: '#receiver_table #add_transporter', q_tip_text: '<?php echo __('Add Transporter');?>',update_row_fct: addTransporter });

	});

</script>
