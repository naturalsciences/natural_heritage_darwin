<table>
	<tr>
		<td>
			<b>Check this box if specimen is concerned by Nagoya protocol:</b><?php echo $form['nagoya']->render() ?>
			<a href='https://darwin.naturalsciences.be/help/nagoya_countries.html' target="popup" onclick="window.open(location.protocol + '//' + location.host + '/help/nagoya.html','popup','width=1150,height=800'); return false;" style="display: inline-block;">
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
		var insert_nagoya=false;
        
       
		var url_nagoya=location + "/../getNagoyaCollection";
        var init_link=function()
        {
            if ($("#specimen_nagoya").val()=="yes")
                {
					$(".nagoya_doc").css('display','inline-block');
				}
        }
		$(document).ready(function () {
			<?php if($form->isNew()):?>
				$isnew=true;
			<?php else:?>
				//GetNagoyaDateAcquisition();
				//GetNagoyaDateSampling("#specimen_gtu");
				//GetNagoyaGTU();
                init_link();                
			<?php endif;?>
			
			$("#specimen_nagoya").change(
                function()
                {
                    init_link();
				
			});	
		});
		
		
		onElementInserted("body",".link_type_selector",
            function(e)
            {
                console.log("new_link");
                
                if(insert_nagoya==true)
                {            
                 
                      $([document.documentElement, document.body]).animate({
                            scrollTop: $(e).offset().top-250
                        }, 500);
                    $(e).val("nagoya");                        
                    insert_nagoya=false;
                }
            }
        );
        
		$('#add_url_nagoya').click(function(event){
            insert_nagoya=true;
            $.when(jQuery('#add_links').click()).done(
                function()
                {
                    console.log("added");
                    var ctrls=$('[id^="spec\_ident\_extlinks\_data\_"]');
                    console.log(ctrls);
                    for (i=0;i<10;i++)
                    {
                        if ($("#specimen_newExtLinks_"+i+"_url").val() == "")
                        {
                                console.log(i);
                        }
                    }
                   
                }
            );
	  
	});
				

		
		$("#specimen_collection_ref").change(function(){
			GetNagoyaCollection(url_nagoya);
           
		});	

	</script>
