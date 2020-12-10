<table>
	<?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
		<tr>
			<td>
				<b>Choose the value defining the link between the specimen with Nagoya protocol:</b><?php echo $form['nagoya']->render() ?>
				<a href="/help/nagoya.html" target="popup" onclick="window.open('/help/nagoya.html','popup','width=1150,height=800'); return false;" style="display: inline-block;">
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
				Above value has been chosen by Darwin (if you didn't change it manually) because:</BR>
				<label id="coll"></label>
			</td>
		</tr>
		<tr class="nagoya_check" style="display: none;">
			<td  colspan="2">
				<?php echo image_tag('attention.jpg',"id=attention");?>
				Above value has been chosen by Darwin (if you didn't change it manually) because:</BR>
			</td>
		</tr>
		<tr class="nagoya_notfilled" style="display: none;">
			<td colspan="2">
				<?php echo image_tag('attention.jpg',"id=attention");?>
				Above value has been chosen by Darwin (if you didn't change it manually) because </BR>
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
	<?php else:?>
		Not allowed
	<?php endif ; ?>
</table>
	<script type="text/javascript">
		var $nagoya  = 0;
		var $val_id = "";
		var $isnew=false;
		
		$(document).ready(function () {
			<?php if($form->isNew()):?>
				$isnew=true;
			//<? php else:?>
			//	GetNagoyaCollection();
			//	GetNagoyaDateAcquisition();
			//	GetNagoyaDateSampling();
			//	GetNagoyaGTU();
			//	setTimeout(function (){ 
			//		fillcheckandlabels(0);}
			//	,1000); 
			<?php endif;?>
			
			$("#specimen_nagoya").change(function(){
				if ($("#specimen_nagoya").val() == "yes"){
					$(".nagoya_doc").css('display','inline-block');
				}
			});	
		});
        /*var path= location.pathname.split("/")[0];
        if(path.length==0)
        {
            path=location.pathname.split("/")[1];
        }
		var url=detect_https(location.protocol + '//' + location.host + "/"+ path + "/specimen/getNagoyaCollection");*/
        var url=detect_https("<?php print(url_for("specimen/getNagoyaCollection")); ?>")
		GetNagoyaCollection(url, $("#specimen_collection_ref").val());
		
		$('#add_url_nagoya').click(function(event){
			jQuery('#add_links').click();
			setTimeout(function() { 
				for (i=0;i<10;i++){
					if ($("#specimen_newExtLinks_"+i+"_url").val() == ""){
							$("#specimen_newExtLinks_"+i+"_url").closest('div').css("display","block"); // widget_content   
							$("#specimen_newExtLinks_"+i+"_url").closest('li').find("div:eq(0)").find("div:eq(0)").css("display","none");		//widget_top_button
							$("#specimen_newExtLinks_"+i+"_url").closest('li').find("div:eq(0)").find("div:eq(1)").css("display","block");		//widget_bottom_button
						$("#specimen_newExtLinks_"+i+"_comment").val("Link to Nagoya directory");
						$("#specimen_newExtLinks_"+i+"_category option[value='nagoya']").attr('selected','selected');
						$("#specimen_newExtLinks_"+i+"_url").focus();
					}
				};
				
			},500); 
		});
				

		

		
		$("#specimen_collection_ref").change(function(){
			//GetNagoyaCollection(url, $("#specimen_collection_ref").val());
			fillcheckandlabels(1);
			/*setTimeout(function (){ 
				fillcheckandlabels(1);}  //0 if called at opening and 1 if called in a change
			,500);*/ 
		});	
	</script>
