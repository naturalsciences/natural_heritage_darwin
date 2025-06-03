 <!--<table class="property_values gtu_countries"  id="gtu_to_country">-->
  <thead style="<?php echo ($form['GtuToCountry']->count() || $form['newGtuToCountry']->count())?'':'display: none;';?>" class="GtuToCountry_head"></thead>
	  <tbody class="body_country_iso">
                <?php $retainedKey = 0;?>
              <?php foreach($form['GtuToCountry'] as $form_value):?>
                <?php include_partial('country_row', array('form' => $form_value, 'ref_id' => ($form->getObject()->isNew() ? '':$form->getObject()->getId()), 'row_num'=>$retainedKey));
        $retainedKey++;?>
              <?php endforeach;?>
              <?php foreach($form['newGtuToCountry'] as $form_value):?>
                <?php include_partial('country_row', array('form' => $form_value, 'ref_id' => ($form->getObject()->isNew() ? '':$form->getObject()->getId()), 'row_num'=>$retainedKey));
        $retainedKey++;?>
              <?php endforeach;?>
            </tbody>
<!--</table>-->
<tr><td><div class="add_country">

           <a class="link_as_button" href="<?php echo url_for("gtu/addGtuToCountry".($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()."&gtu_ref=".$form->getObject()->getId()) );?>/num/" id="add_country"><?php echo __('Add country');?></a>
<div>
<script  type="text/javascript">
$(document).ready(function () {
	var old_val="";
	  $(".select_iso_country").live("change",
	  
		function()
		{
			//console.log("detected");
			//console.log($(this).val());
			//set_iso3166($(this).find("option:selected").text());
			  var ctry_list=Array();
			  $(".select_iso_country").each(function(){
				  console.log($(this).find("option:selected").text());
				  var tmp_val=$(this).find("option:selected").text();
				  var tmp=tmp_val.split("-");
					if(tmp.length>1)
					{
							tmp.shift();
							tmp_val=tmp.join("-");
							ctry_list.push(tmp_val);
					}
			  });
			console.log(ctry_list.join(";"));
			set_iso3166(ctry_list.join(";"));
			  
		}
	  );


       var set_iso3166= function(tmp_val) {
		   console.log(tmp_val);
		    
			   $( "select[name*='sub_group_name']" ).each( 
				
					function()
					{                
						if($(this).val().toLowerCase()=="country")
						{
							
							var idx = $(this).attr('id').match(/\d+/)[0];
								console.log(idx);
							if($("#gtu_newVal_"+idx+"_tag_value").length)
							{
								if($("#gtu_newVal_"+idx+"_tag_value").val().trim()==""||tmp_val.includes($("#gtu_newVal_"+idx+"_tag_value").val()) ||$("#gtu_newVal_"+idx+"_tag_value").val()==old_val)
								{
									$("#gtu_newVal_"+idx+"_tag_value").val(tmp_val);
									old_val=$("#gtu_newVal_"+idx+"_tag_value").val();
								}
							}
							if($("#gtu_TagGroups_"+idx+"_tag_value").length)
							{							
								if($("#gtu_TagGroups_"+idx+"_tag_value").val().trim()==""||tmp_val.includes($("#gtu_TagGroups_"+idx+"_tag_value").val())||$("#gtu_TagGroups_"+idx+"_tag_value").val()==old_val)
								{
									$("#gtu_TagGroups_"+idx+"_tag_value").val(tmp_val);
									old_val=$("#gtu_TagGroups_"+idx+"_tag_value").val();
								}
							}           
		   
						}
					}
				);
    };
	
	
    $('#add_country').click( function()
    {

        $.ajax(
        {
          type: "GET",
          url: $(this).attr('href')+ (0+$('.body_country_iso tr').length),
          success: function(html)
          {            

            $(".body_country_iso").append(html);

          }
        });
        return false;
    }
	
	); 
	
	<?php if($form->getObject()->isNew()):?>
		 $('#add_country').click();
	<?php endif;?>
});
</script>
</td></tr>
