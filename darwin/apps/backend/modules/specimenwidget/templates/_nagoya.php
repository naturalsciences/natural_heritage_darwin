<table>
	<tr>
		<td>
			<b>Check this box if specimen is concerned by Nagoya protocol:</b><?php echo $form['nagoya']->render() ?>
			<a href=location.protocol + '//' + location.host + '/'+ location.pathname.split('/')[1] +'/help/nagoya.html' target="popup" onclick="window.open(location.protocol + '//' + location.host + '/'+ location.pathname.split('/')[1] +'/help/nagoya.html','popup','width=1150,height=800'); return false;" style="display: inline-block;">
				<?php echo image_tag('info.png',"title=nagoya_info class=nagoya_info id=nagoya");?>
			</a> 
			<?php echo $form['nagoya']->renderError(); ?>
		</td>
		<td style="display: none;">
			<input type="text" id="date_acq">
			<input type="text" id="date_sampl">
			<input type="text" id="gtu">
			<input type="text" id="coll" >
		</td>
	</tr>
	<tr class="nagoya_uncheck" style="display: none;">
		<td colspan="2">
			<?php echo image_tag('attention.jpg',"id=attention");?>
			Nagoya box is automatically unchecked because:</BR>
			<label id="coll"></label>
		</td>
	</tr>
	<tr class="nagoya_check" style="display: none;">
		<td  colspan="2">
			<?php echo image_tag('attention.jpg',"id=attention");?>
			Nagoya box is automatically checked because:</BR>
		</td>
	</tr>
	<tr class="nagoya_notfilled" style="display: none;">
		<td colspan="2">
			<?php echo image_tag('attention.jpg',"id=attention");?>
			Nagoya status can't be determined automatically because: </BR>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<label id="coll_label"></label></BR>
			<label id="GTU_label"></label></BR>
			<label id="dates_label"></label>
		</td>
	</tr>
	<tr class="nagoya_verify" style="display: none;">
		<td colspan="2">
			Please verify the validity of that automatic processing
		</td>
	</tr>
	<tr>
		<td class="nagoya_doc" colspan="2" style="display: none;">
			<input type="button" id="add_url_nagoya" value="Add link to Nagoya directory"></input>
		</td>
	</tr>
</table>
	<script type="text/javascript">
		var $nagoya  = 0;
		var $val_id = "";
		var $isnew=false;
		
		$(document).ready(function () {
			<?php if($form->isNew()):?>
				$isnew=true;
			<?php else:?>
				GetNagoyaCollection();
				GetNagoyaDateAcquisition();
				GetNagoyaDateSampling();
				GetNagoyaGTU();
				setTimeout(function (){ 
					fillcheckandlabels(0);}
				,500); 
			<?php endif;?>
			
			$("#specimen_nagoya").change(function(){
				if ($("#specimen_nagoya").prop('checked')){
					$(".nagoya_doc").css('display','inline-block');
				}
			});	
		});
		
		GetNagoyaCollection();
		
		$('#add_url_nagoya').click(function(event){
			jQuery('#add_links').click();
			setTimeout(function() { 
				for (i=0;i<10;i++){
					if ($("#specimen_newExtLinks_"+i+"_url").val() == ""){
							$("#specimen_newExtLinks_"+i+"_url").closest('div').css("display","block"); // widget_content   
							$("#specimen_newExtLinks_"+i+"_url").closest('li').find("div:eq(0)").find("div:eq(0)").css("display","none");		//widget_top_button
							$("#specimen_newExtLinks_"+i+"_url").closest('li').find("div:eq(0)").find("div:eq(1)").css("display","block");		//widget_bottom_button
						$("#specimen_newExtLinks_"+i+"_comment").val("Link to Nagoya directory");
						$("#specimen_newExtLinks_"+i+"_type option[value='nagoya']").attr('selected','selected');
						$("#specimen_newExtLinks_"+i+"_url").focus();
					}
				};
				
			},500); 
		});
				
		function GetNagoyaCollection(){
			var url=location.protocol + '//' + location.host + "/"+ location.pathname.split("/")[1] + "/"+ location.pathname.split("/")[2]+ "/specimen/getNagoyaCollection";
			$.getJSON( 
				url,
				{id: $("#specimen_collection_ref").val()},
				function(data) {
					if(data.nagoya == "yes"){
						$('#coll').val("ok");
					}else if(data.nagoya == "no"){
							$('#coll').val("nok");
					}else{
						$('#coll').val("");
					}
				}
			);
		};
		
		function fillcheckandlabels($origin) { 
			if(	$('#coll').val()!="" & $('#gtu').val()!=""  & $('#date_sampl').val()!="" & $('#date_acq').val()!=""){
				if(	$('#coll').val()=="ok" & $('#gtu').val()=="ok"  & ($('#date_sampl').val()=="ok" | $('#date_acq').val()=="ok") ){
					if (($origin == 0 & $isnew)| $origin == 1){
						$('.nagoya').prop( "checked", true );	
					}
					$(".nagoya_uncheck").hide();
					$(".nagoya_check").show();
					$(".nagoya_doc").show();
					$(".nagoya_notfilled").hide();
					$(".nagoya_verify").show();
					
					$("#coll_label").text("- Collection is concerned by Nagoya protocol");
					$("#GTU_label").text("- Sampling location is in a area concerned by Nagoya protocol");
					$("#dates_label").text("- Dates of acquisition and/or collect are after 12/10/2014");
				}else{
					if (($origin == 0 & $isnew)| $origin == 1){
						$('.nagoya').prop( "checked", false );	
					}
					$(".nagoya_uncheck").show();
					$(".nagoya_check").hide();
					$(".nagoya_doc").hide();
					$(".nagoya_notfilled").hide();
					$(".nagoya_verify").show();
					
					if(	$('#coll').val()=="ok"){
						$("#coll_label").text("- Collection is concerned by Nagoya protocol");
					}else{
						$("#coll_label").text("- Collection is NOT concerned by Nagoya protocol");
					}
					
					if(	$('#gtu').val()=="ok"){
						$("#GTU_label").text("- Sampling location is in a area concerned by Nagoya protocol");
					}else{
						$("#GTU_label").text("- Sampling location is NOT in a area concerned by Nagoya protocol");
					}
					
					if($('#date_sampl').val()=="ok" | $('#date_acq').val()=="ok") {
						$("#dates_label").text("- Dates of acquisition and/or collect are after 12/10/2014");
					}else{
						$("#dates_label").text("- Dates of acquisition and collect are BEFORE 12/10/2014");
					}
				}
			}else{
				if (($origin == 0 & $isnew)| $origin == 1){
					$('.nagoya').prop( "checked", false );	
				}
				$(".nagoya_uncheck").hide();
				$(".nagoya_check").hide();
				$(".nagoya_doc").hide();
				$(".nagoya_notfilled").show();
				$(".nagoya_verify").show();
				
				if(	$('#coll').val()==""){
					$("#coll_label").text("- Collection is NOT filled");
				}else if($('#coll').val()=="ok"){
						$("#coll_label").text("- Collection is concerned by Nagoya protocol");
				}else{
					$("#coll_label").text("- Collection is NOT concerned by Nagoya protocol");
				}
				
				if(	$('#gtu').val()==""){
					$("#GTU_label").text("- Sampling location is NOT chosen");
				}else if($('#gtu').val()=="ok"){
					$("#GTU_label").text("- Sampling location is in a area concerned by Nagoya protocol");
				}else{
					$("#GTU_label").text("- Sampling location is NOT in a area concerned by Nagoya protocol");
				}
				
				if($('#date_sampl').val()=="" | $('#date_acq').val()=="") {
					$("#dates_label").text("- Date of acquisition and/or collect are NOT filled");
				}else if($('#date_sampl').val()=="ok" | $('#date_acq').val()=="ok") {
					$("#dates_label").text("- Dates of acquisition and/or collect are after 12/10/2014");
				}else{
					$("#dates_label").text("- Dates of acquisition and collect are BEFORE 12/10/2014");
				}
			}				
		};
		
		$("#specimen_collection_ref").change(function(){
			GetNagoyaCollection();
			setTimeout(function (){ 
				fillcheckandlabels(1);}  //0 if called at opening and 1 if called in a change
			,500); 
		});	
	</script>
