<tr class="loanlines_top" >
  <td colspan="9"></td>
</tr>
<tr class="line_<?php echo $form->getparent()->getName().'_'.$form->getName();?>">
  <td>
    <?php echo $form->renderError();?>
    <?php if(!$lineObj->isNew()):?>
      <input value="<?php echo $lineObj->getId();?>" type="checkbox" class="select_chk_box" />
    <?php endif;?>
  </td>
  <td>
    <?php echo image_tag('info.png',"class=extd_info");?>
    <?php echo $form['specimen_ref']->renderError();?>
    <?php echo $form['specimen_ref'];?>
  </td>
  <td>
  <!--JMHerpers 2018 04 19-->
    <a  target='_blank' href='<?php echo url_for('specimen/view?id='.$form['specimen_ref']->getValue());?>'>
		<?php echo include_component('specimenwidgetview', 'refMainCodes', array('eid'=>$form['specimen_ref']->getValue()));?> 
	</a>
  </td>
  <td style="font-size:11px">
    <?php echo include_component('specimenwidgetview', 'refTaxon', array('eid'=>$form['specimen_ref']->getValue()));?>
  </td>
  <td colspan="2">
    <?php echo $form['details']->renderError();?>
    <?php echo $form['details'];?>
  </td>
  <td>
    <?php echo $form['from_date']->renderError();?>
    <?php echo $form['to_date']->renderError();?>
    <?php echo $form['loan_item_ind'];?>


    <div class="loan_item_date_button" >
      <input type="checkbox"  <?php if( $form['from_date']->getValue() ||  $form['to_date']->getValue()) print('checked="checked"')  ;?> />
      <label><?php echo __('Check if returned separately');?></label>
    </div>
    <br />
    <div class="loan_item_dates <?php if(! $form['from_date']->getValue() &&  !$form['to_date']->getValue() ) echo 'hidden';?>">
      <?php echo $form['from_date']->renderLabel();?><br />
      <?php echo $form['from_date'];?><br />
      <?php echo $form['to_date']->renderLabel();?><br />
      <?php echo $form['to_date'];?><br />
    </div>
  </td>
  <td class="loan_actions_button">
    <?php echo $form['item_visible'];?>
    <!--<?php if(! $lineObj->isNew()):?>
      <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'loanitem/view?id='.$lineObj->getId());?>
      <?php echo link_to(image_tag('edit.png', array("title" => __("Edit"))),'loanitem/edit?id='.$lineObj->getId());?>
    <?php endif;?>-->
  </td>

  <td class="item_row_delete">
      <?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_code_'.($lineObj->isNew() ? 'n_' : 'o_').$form->getName()); ?>
  </td>
</tr>
<script type="text/javascript">
  $(document).ready(function () {

	  var nbrtot = 0;
    $(".line_<?php echo $form->getparent()->getName().'_'.$form->getName();?> .loan_item_date_button input").change(function(event)
    {
      if($(this).is(':checked')){
		 
		  var prefix="#loan_overview_LoanItems_<?php echo $form->getName();?>_to_date_";
		  var today=new Date();
		  $(prefix+'year').val(today.getFullYear());
		  $(prefix+'month').val(today.getMonth() + 1);
		  $(prefix+'day').val(String(today.getDate()));//.padStart(2, '0'));
		 $(this).closest('td').find('.loan_item_dates').show();        
      }
      else {
		  
		 var prefix="#loan_overview_LoanItems_<?php echo $form->getName();?>_to_date_";
		 var prefixfrom="#loan_overview_LoanItems_<?php echo $form->getName();?>_from_date_";
		 
		 $(prefixfrom+'year').val($(prefixfrom+'year option:first').val());
		 $(prefixfrom+'month').val($(prefixfrom+'month option:first').val());
		 $(prefixfrom+'day').val($(prefixfrom+'day option:first').val());
		 $(prefix+'year').val($(prefix+'year option:first').val());
		 $(prefix+'month').val($(prefix+'month option:first').val());
		 $(prefix+'day').val($(prefix+'day option:first').val());
        //$(this).closest('td').find('.loan_item_dates').hide();
        //$(this).closest('td').find('.loan_item_dates select,.loan_item_dates input ').val('');
      }
    });

    $(".line_<?php echo $form->getparent()->getName().'_'.$form->getName();?> [id$=\"_specimen_ref\"]").change(function(){
      el = $(this);
      $.getJSON('<?php echo url_for('loanitem/getIgNum');?>', {id : $(this).val() }, function( data) {
        ig_ref = el.closest('tr').find('[id$=\"_ig_ref\"]');
        ig_name = el.closest('tr').find('[id$=\"_ig_ref_name\"]');
        ig_ref.val(data.ig_ref);
        ig_name.val(data.ig_num);
      });  
    });

    $("#clear_code_<?php echo ($lineObj->isNew() ? 'n_' : 'o_').$form->getName();?>").click( function()
    {
      parent_el = $(this).closest('tr');
      parent_el.hide();
      parent_el.next().hide();
      parent_el.next().next().hide();
      parent_el.find('input[type="hidden"][id$=\"_item_visible\"]').val('');
      if($('.loan_overview_form > table tbody > tr:visible').length ==0){
        $('.loan_overview_form > table').addClass('hidden');
         $('.warn_message').removeClass('hidden');
      }
    });
		  
    bind_ext_line('<?php echo $form->getparent()->getName();?>',  '<?php echo $form->getName();?>')

    $(".line_<?php echo $form->getparent()->getName().'_'.$form->getName();?> [id$=\"_ig_ref_check\"]").change(function(){
      if($(this).val()) 
      {
        $.ajax({
          type: 'POST',
          url: "<?php echo url_for('igs/addNew') ?>",
          data: "num=" + $(".line_<?php echo $form->getparent()->getName().'_'.$form->getName();?> [id$=\"_ig_ref_name\"]").val(),
          success: function(html){
            $(".line_<?php echo $form->getparent()->getName().'_'.$form->getName();?> li#toggledMsg").hide();
            $(".line_<?php echo $form->getparent()->getName().'_'.$form->getName();?> [id$=\"_ig_ref\"]").val(html) ;
          }
        });  
      }
    });

    <?php if(!$lineObj->isNew()):?>
		$(".main_but_line_<?php echo $form->getparent()->getName().'_'.$form->getName();?> .maint_butt").click(function(){
		  but_link = $(this);
		  el = $(".maint_line_<?php echo $form->getparent()->getName().'_'.$form->getName();?> .maintenance_details");
		  if(! el.is(':visible')) {
			$.ajax({
			  url: "<?php echo url_for('loanitem/showmaintenances');?>",
			  data: { id: <?php echo $lineObj->getId();?> },
			  success: function(html){
				$(el).html(html);
				el.show();
				//but_link.find('img').attr('src','<?php echo url_for('/images/individual_expand_up.png');?>');
			  }
			});
		  } else {
			el.hide();
			//$(this).find('img').attr('src','<?php echo url_for('/images/individual_expand.png');?>');
		  }
		});
		
		//JMHerpers 2018 05 03 following code to add choice of only parts of specimen
		var el2 = $(".part_line1_<?php echo $form->getparent()->getName().'_'.$form->getName();?>");
		var el1 = el2.parent().children(".line_<?php echo $form->getparent()->getName().'_'.$form->getName();?>").find("td:eq(5)").children(0); //field details
		var el3 = el2.children().children('.part_details');
		var el3b = el2.children(".showchosen1_<?php echo $form->getName();?>");
		var el4 = $(".part_line2_<?php echo $form->getparent()->getName().'_'.$form->getName();?>");
		var el5 = el4.children(".showchosen2_<?php echo $form->getName();?>");
		//var el6 = el5.children('.label_spec_part').children(0).children(0).children(0); //checkbox
		
		//show label lines for specimen or parts changes
		var spc = el3b.children('.specimen_count').val();
		if( spc != ""){
			el3b.append("<font size='6'><i><label>(Specimens chosen:"+spc+")</label></i></font>");
		}	
		var spp = el5.children('.specimen_part').val();
		if( spp != ""){
			el5.append("<font size='6'><i><label>(Parts chosen:"+spp+")</label></i></font>");
		}
		
		var nbrres = "<?php $q = Doctrine_Query::create()
					->select('*')
					->from('Specimens')
					->where('id = ?',$form['specimen_ref']->getValue());
					$result =$q->FetchOne();?>";
			
		var nbrmales = "<?php echo($result->getSpecimenCountMalesMin());?>";
		var nbrfemales = "<?php echo($result->getSpecimenCountFemalesMin());?>";
		var nbrjuveniles = "<?php echo($result->getSpecimenCountJuvenilesMin());?>";
		var nbrtot = "<?php echo($result->getSpecimenCountMin());?>";
		
		if (nbrtot == ''){
			nbrtot = 0;
		}
		if (nbrmales == ''){
			nbrmales = 0;
		}
		if (nbrfemales == ''){
			nbrfemales = 0;
		}
		if (nbrjuveniles == ''){
			nbrjuveniles = 0;
		}
		el3.children('.specimen_count_tot').val(nbrtot);
		el3.children('.specimen_count_males').val(nbrmales);
		el3.children('.specimen_count_females').val(nbrfemales);
		el3.children('.specimen_count_juveniles').val(nbrjuveniles);

		var textcountMFJ = "";	
		function create_numbers_string(){
			var textcountMFJ = "(";			
			
			if (el3.children('.specimen_count_males').val() + el3.children('.specimen_count_females').val() + el3.children('.specimen_count_juveniles').val() == 0){
				textcountMFJ = "";
			}
			else{
				if (el3.children('.specimen_count_males').val() != 0){
					textcountMFJ = textcountMFJ + el3.children('.specimen_count_males').val() + "M ";
				}
				if (el3.children('.specimen_count_females').val() != 0){
					if (el3.children('.specimen_count_males').val() != 0){
						textcountMFJ = textcountMFJ + " + " + el3.children('.specimen_count_females').val()+ "F";
					}
					else{
						textcountMFJ = textcountMFJ + el3.children('.specimen_count_females').val()+ "F";
					}
				}
				if (el3.children('.specimen_count_juveniles').val() != 0){
					if ((el3.children('.specimen_count_males').val() != 0) | (el3.children('.specimen_count_females').val() != 0)){
						textcountMFJ = textcountMFJ + " + " + el3.children('.specimen_count_juveniles').val()+"Juv";
					}
					else{
						textcountMFJ = textcountMFJ + el3.children('.specimen_count_juveniles').val()+"Juv";
					}
				}
				textcountMFJ = textcountMFJ + ")";
			}		
			return textcountMFJ;
		}
		
		create_numbers_string();
		el3.children('.specimen_count_tot').focusout(function() {
			el3b.children('.specimen_count').val("Tot. nbr:"+el3.children('.specimen_count_tot').val() + create_numbers_string());
		});
		el3.children('.specimen_count_males').focusout(function() {
			el3b.children('.specimen_count').val("Tot. nbr:"+el3.children('.specimen_count_tot').val() + create_numbers_string());
		});
		el3.children('.specimen_count_females').focusout(function() {
			el3b.children('.specimen_count').val("Tot. nbr:"+el3.children('.specimen_count_tot').val() + create_numbers_string());
		});
		el3.children('.specimen_count_juveniles').focusout(function() {
			el3b.children('.specimen_count').val("Tot. nbr:"+el3.children('.specimen_count_tot').val() + create_numbers_string());
		});
		
		// storage parts------------------------------------------
		var nbrres = "<?php $query = 'SELECT * FROM Storage_parts WHERE specimen_ref ='.$form['specimen_ref']->getValue().' ORDER BY id';
							$rs = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAssoc($query);
						
							$i=0;
							foreach($rs as $result)
							{
								$results[$i]['container'] = $result['container'];
								$results[$i]['container_type'] = $result['container_type'];
								$results[$i]['container_storage'] = $result['container_storage'];
								$results[$i]['object_name'] = $result['object_name'];
								$results[$i]['specimen_part'] = $result['specimen_part'];
								$results[$i]['specimen_status'] = $result['specimen_status'];
								$i++;
							}
					  ?>";
		var arrayFromPHP = <?php echo json_encode($results); ?>;
		
		var length_results = arrayFromPHP.length;

		if (length_results >= 2) {
			var index = 0;
			var namestr = "";
			var containerstr = "";
			
			for (i=0;i < length_results ;i++){
				namestr = "";
				containerstr = "";
				var partname = "";
				index = i +1;
								
				if (trim(arrayFromPHP[i]['object_name']) == ""){
					partname = arrayFromPHP[i]['specimen_part'];
				}else {partname = arrayFromPHP[i]['object_name'];
				}
				if (trim(partname) != ""){
					namestr = ":Name ="+partname;
				}

				if (trim(arrayFromPHP[i]['container']) != ""){
					containerstr = " (Container :"+arrayFromPHP[i]['container']+")";
				}
				strpart = "Part nr "+(index)+ namestr + containerstr;

				var contentrow = "<tr><td>"+index+"</td>"+
								"<td>"+partname+"</td>"+
								"<td>"+arrayFromPHP[i]['container']+"</td>"+
								"<td>"+arrayFromPHP[i]['container_type']+"</td>"+
								"<td>"+arrayFromPHP[i]['container_storage']+"</td>"+
								"<td>"+arrayFromPHP[i]['specimen_status']+"</td>"+
								"<td class='td_<?php echo $form->getName();?>_"+i+"'>"+
									"<input type='checkbox' id='checkbox_<?php echo $form->getparent()->getName().'_'.$form->getName();?>_"+i+"' name='checkbox_<?php echo $form->getparent()->getName().'_'.$form->getName();?>' value='"+strpart+"'/>"+
								"</td>"+
								"</tr>";
				$(".specpart_loans_<?php echo $form->getName();?> tbody").append(contentrow);
			}
		}

		var myRow = 0;
		var strcheck2 = "";
		var strcheck = 'checkbox_<?php echo $form->getparent()->getName().'_'.$form->getName();?>';
					
		$('[name='+strcheck+']').click(function () {
				checkAll();
			});

		function checkAll() {
			el5.children('.specimen_part').val("");
			$('[name='+strcheck+']:checked').each(function () {
				el5.children('.specimen_part').val(el5.children('.specimen_part').val() + $(this).val()+ " +");
			});
			el5.children('.specimen_part').val(el5.children('.specimen_part').val().trim().substr(0,el5.children('.specimen_part').val().trim().length-1));
		}
		
		$("#part_butt_id_<?php echo $form->getparent()->getName().'_'.$form->getName();?>").click(function(){
			if(! el2.is(':visible')) {
				el2.css("display", "");
			} else {
				el2.css("display", "none");
			}
			if (length_results < 2) {
				el4.css("display", "none");
			}else{
				if(! el4.is(':visible')) {
					el4.css("display", ""); 
				} else {
					el4.css("display", "none");
				}
			}
		});
    <?php endif;?>
  });
</script>

<tr style="font-size:11px" class="main_but_line_<?php echo $form->getparent()->getName().'_'.$form->getName();?>">
  <td colspan="9" style="text-align:right">	
	<a class="maint_butt<?php if($lineObj->isNew()) echo 'disabled';?>" href="#">
      <!--<php echo image_tag( ($lineObj->isNew() ? 'grey' : 'individual' ).'_expand.png');?> -->
	  <?php echo __('Maintenances');?>
    </a>&nbsp;&nbsp;&nbsp;&nbsp;
	<a id="part_butt_id_<?php echo $form->getparent()->getName().'_'.$form->getName();?>" class="part_butt<?php if($lineObj->isNew()) echo 'disabled';?>" href="#">
      <?php echo __('Export only part of package');?>
    </a></td>
</tr>

<tr class="maintenance_table_line maint_line_<?php echo $form->getparent()->getName().'_'.$form->getName();?>">
  <td colspan="4"></td>
  <td colspan="5">
	<div class="maintenance_details"></div>
  </td>
</tr>

<tr class="part_line1_<?php echo $form->getparent()->getName().'_'.$form->getName();?>" style="display:none;" >
  <td colspan="3"></td>
  <th colspan="6">
	<div class="part_details" style="text-align:left">
		<?php echo 'Change number of specimens to loan:' ?>
		<?php echo 'Total :' ?> <?php echo $form['specimen_count_tot']->renderError(); echo $form['specimen_count_tot'] ;?>
		<?php echo 'Males :' ?> <?php echo $form['specimen_count_males']->renderError(); echo $form['specimen_count_males'] ;?>
		<?php echo 'Females :' ?> <?php echo $form['specimen_count_females']->renderError(); echo $form['specimen_count_females'] ;?>
		<?php echo 'Juveniles :' ?> <?php echo $form['specimen_count_juveniles']->renderError(); echo $form['specimen_count_juveniles'] ;?>
	</div>
  </th>
</tr>

<tr class="part_line1_<?php echo $form->getparent()->getName().'_'.$form->getName();?>" style="display:none;" >
  <td colspan="9" class="showchosen1_<?php echo $form->getName();?>" style="text-align:right">
		<?php echo $form['specimen_count']->renderError(); echo $form['specimen_count'] ;?>
  </td>
</tr>
<tr class="part_line2_<?php echo $form->getparent()->getName().'_'.$form->getName();?>" style="display:none;" >
  <td colspan="3"></td>
  <td colspan="6">
	<div class="spec_part">
		<b><?php echo 'Specimen parts :' ?></b> 
		<table class = "specpart_loans_<?php echo $form->getName();?> specpart_loans" width="100%">
			<tr>
				<th>Part nr</th>
				<th>Part name</th>
				<th>Container number</th>
				<th>Type</th>
				<th>Substance in container</th>
				<th>Part status</th>
				<th></th>
			</tr>
		</table>
	</div>
  </td>
</tr>

<tr class="part_line2_<?php echo $form->getparent()->getName().'_'.$form->getName();?>" style="display:none;" >
  <td colspan="9" class="showchosen2_<?php echo $form->getName();?>" style="text-align:right">
	<?php echo $form['specimen_part']->renderError(); echo $form['specimen_part'] ;?>
  </td>
</tr>
<tr class="loanlines_bottom" >
  <td colspan="9"></td>
</tr>
