<?php if($form->isValid()):?>
<?php if(isset($items) && $items->count() != 0):?>
  <?php
    if($orderDir=='asc')
      $orderSign = '<span class="order_sign_down">&nbsp;&#9660;</span>';
    else
      $orderSign = '<span class="order_sign_up">&nbsp;&#9650;</span>';
  ?>
  <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
  <?php include_partial('global/pager_info', array('form' => $form, 'pagerLayout' => $pagerLayout)); ?>
  <div class="results_container gtu_results">
    <table class="results <?php if($is_choose) echo 'is_choose';?>">
      <thead>
		  <th><!-- Pin -->
              <label class="top_gtu_pin top_pin"><?php print(__("Select all")); ?><input type="checkbox" /></label>
            </th>
          <th>
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=code'.( ($orderBy=='code' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Code');?>
              <?php if($orderBy=='code') echo $orderSign ?>
            </a>
          </th>
          <th><?php echo __('Location');?></th>
          <th class="hidden"></th>
          <th><?php echo __('Latitude');?></th>
          <th><?php echo __('Longitude');?></th>
		  <!--ftheeten date removed-->
          <th>
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=elevation'.( ($orderBy=='elevation' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Elevation');?>
              <?php if($orderBy=='elevation') echo $orderSign ?>
            </a>
          </th>
          <th></th>
      </thead>
      <tbody>
        <?php foreach($items as $item):?>
		  
          <tr class="rid_<?php echo $item->getId();?>">
		   <td>
                <label class="pin"><input type="checkbox" value="<?php echo $item->getId();?>" <?php if($sf_user->isPinned($item->getId(), 'gtu')):?>checked="checked"<?php endif;?> /></label>
            </td>
            <td class="top_aligned gtu_code"><?php echo $item->getCode();?>
			 
            <td class=""><?php echo $item->getName(ESC_RAW);?>
			<!--ftheeten 2018 12 2-->
		    <?php if(strlen(trim($item->getComments()))>0):?>
            <div>
					 <ul class='search_tags'>
                     <li>
                        <ul class='name_tags_view'>
                         <?php if(strlen(trim($item->getComments()))>0):?>
                            <?php foreach(explode("|", $item->getComments()) as $comment):?>
                            <li><?php print($comment);?></li>
                            <?php endforeach;?>
                            <?php endif;?>
                        </ul>
                        </li>
                    </ul>
			        <br/>

                   </ul>
             </div>
             <?php endif;?>
             <?php if(strlen(trim($item->getProperties()))>0):?>
            <div>
					 <ul class='search_tags'>
                     <li>
                        <ul class='name_tags_view'>
                         <?php if(strlen(trim($item->getProperties()))>0):?>
                            <?php foreach(explode("|", $item->getProperties()) as $property):?>
                            <li><?php print($property);?></li>
                            <?php endforeach;?>
                            <?php endif;?>
                        </ul>
                        </li>
                    </ul>
			        <br/>

                   </ul>
             </div>
             <?php endif;?>
             <!--ftheeten 2019 07 01-->
             
             <?php if(count($item->getRelatedTemporalInformationMasked())>0):?>          
                         <ul >
                        <li  >
                        <br/>
                        Date : 
                        <select id="select_gtu_date_id_<?php print($item->getId())?>">
                            <?php $iRow=0;?>
                            <?php foreach($item->getRelatedTemporalInformationMasked(ESC_RAW) as $date):?>
                                <option value="<?php print($item->getId());?>_<?php print($iRow);?>">From : <?php print($date["from_masked_select"]);?>
                                <?php if($date["to_mask"]>0):?> - To : <?php print($date["to_masked_select"]);?>            
                                <?php endif; ?>                                       
                                </option>
                                <?php $iRow++;?>
                            <?php endforeach;?>      
                        </select> ( <?php print(count($item->getRelatedTemporalInformationMasked()));?> Value(s))
                            <table style="display:none">
                            <?php $iRow=0;?>
                             <?php foreach($item->getRelatedTemporalInformationMasked(ESC_RAW) as $date):?>
                             <tr id="dateholder_<?php print($item->getId());?>" name="dateholder_<?php print($item->getId());?>"   style="display:none">
                             <td  class="item_name date_choose" style="display:none"><?php print($item->getId());?></td>
                             <td  id="temporalinformation_gtu_id_<?php print($item->getId());?>"   name="temporalinformation_gtu_id_<?php print($item->getId());?>" class="temporal_information_value date_choose temporalinformation_gtu_id_<?php print($item->getId());?> temporalinformation_date_id_<?php print($item->getId());?>_<?php print($iRow);?>" style="display:none">
                                    <b class="date_from" style="display:none"><?php print($date["from_masked"]);?></b>
                                    <b class="date_to" style="display:none"><?php print($date["to_masked"]);?></b>                             
                                    <b class="date_from_mask" style="display:none"><?php print($date["from_mask"]);?></b>
                                    <b class="date_from_year" style="display:none"><?php print($date["from_year"]);?></b>
                                    <b class="date_from_month" style="display:none" ><?php print($date["from_month"]);?></b>
                                    <b class="date_from_day" style="display:none"><?php print($date["from_day"]);?></b>
                                    <b class="date_from_hour" style="display:none"><?php print($date["from_hour"]);?></b>
                                    <b class="date_from_minute" style="display:none"><?php print($date["from_minute"]);?></b>
                                    <b class="date_from_second" style="display:none" ><?php print($date["from_second"]);?></b>
                                    <b class="date_to_mask" style="display:none" ><?php print($date["to_mask"]);?></b>
                                    <b class="date_to_year" style="display:none" ><?php print($date["to_year"]);?></b>
                                    <b class="date_to_month" style="display:none"><?php print($date["to_month"]);?></b>
                                    <b class="date_to_day" style="display:none"><?php print($date["to_day"]);?></b>
                                    <b class="date_to_hour" style="display:none"><?php print($date["to_hour"]);?></b>
                                    <b class="date_to_minute" style="display:none" ><?php print($date["to_minute"]);?></b>
                                    <b class="date_to_second" style="display:none"><?php print($date["to_second"]);?></b>
                            </td ></tr>
                             <?php $iRow++;?>
                            <?php endforeach;?>
                            </table>   
                            <?php if($is_choose && !strpos($referer,"/staging/edit/id")):?>                             
                                 <br/><div  name="date_choose" class="result_choose"><?php echo __('Choose place and date');?></div>                                   
                             <?php endif;?>                            
                              <?php if($is_choose && !strpos($referer,"/staging/edit/id")):?>                              
                               <br/><div name="gtu_choose" id="gtu_choose_<?php print($item->getId());?>" class="result_choose"><?php echo __('Choose place without date');?></div>               
                              <?php endif;?>
                            
                            
                            </li>                   
                   <?php elseif($is_choose && !strpos($referer,"/staging/edit/id")):?>
                    <br/><div name="gtu_choose" id="gtu_choose_<?php print($item->getId());?>" class="result_choose"><?php echo __('Choose place without date');?></div>  
                   <?php endif;?>
                   <?php if(strpos($referer,"/staging/edit/id")):?>
                    <br/><div name="gtu_choose" class="result_choose update_staging"><?php echo __('Choose place id');?></div>  
                   <?php endif;?>
			</td>
			</td>
            <td id="tags_with_code" class="item_name hidden class_gtu_id_<?php print($item->getId());?>"><?php echo $item->getTagsWithCode(ESC_RAW);?>
				
			</td>
            <td class=""><?php echo $item->getLatitude();?></td>
            <td class=""><?php echo $item->getLongitude();?></td>
           
            <td class=""><?php echo $item->getElevation();?></td>
            <td class="<?php echo (! $is_choose)?'edit':'choose';?> top_aligned">
			   <?php if(! $is_choose):?>
                <?php echo link_to(image_tag('blue_eyel.png', array("title" => __('View'))),'gtu/view?id='.$item->getId(),array('target'=>'_blank'));?>
                <?php echo link_to(image_tag('edit.png',array('title' => 'Edit')),'gtu/edit?id='.$item->getId(),array('target'=>'_blank'));?>
                <?php echo link_to(image_tag('duplicate.png',array('title' => 'Duplicate')),'gtu/new?duplicate_id='.$item->getId(),array('target'=>'_blank'));?>
              <?php else:?>                
                <?php echo link_to(image_tag('blue_eyel.png', array("title" => __('View'))),'gtu/view?id='.$item->getId(),array('target'=>'_blank'));?>
                <?php echo link_to(image_tag('edit.png',array('title' => 'Edit')),'gtu/edit?id='.$item->getId(),array('target'=>'_blank'));?>
                <?php echo link_to(image_tag('duplicate.png',array('title' => 'Duplicate')),'gtu/new?duplicate_id='.$item->getId(),array('target'=>'_blank'));?>
              <?php endif;?>
            </td>
          </tr>
		 
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
  <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
<?php else:?>
  <?php echo __('No Matching Items');?>
<?php endif;?>

<?php else:?>
  <div class="error">
    <?php echo $form->renderGlobalErrors();?>
    <?php echo $form['code']->renderError() ?>
    <?php echo $form['gtu_from_date']->renderError() ?>
    <?php echo $form['gtu_to_date']->renderError() ?>
</div>
<?php endif;?>
<script type="text/javascript">

var geosjon='<?php print(htmlspecialchars_decode($geo_obj_json)); ?>';

function pin_gtu(ids, status) {
  var id_part = "";
  if( Object.prototype.toString.call( ids ) === '[object Array]' ) {
    id_part = '/mid/' + ids.join(",");
  } else {
    id_part = '/id/' + ids;
  }
  $.getJSON('<?php echo url_for('savesearch/pin?source=gtu');?>'+ id_part + '/status/' + ( status ? '1':'0'),function (data){
    if(data.pinned) {
      $('.pinned_specimens i').text('(' + Object.keys(data.pinned).length + ')');
    }
  });
}

$(document).ready(function () {
 /* //Init screen size
  check_screen_size();

  //Init resize of screen
  $(window).resize(check_screen_size);

  //Init columns visibilty
  $('ul.column_menu .col_switcher :not(:checked)').each(function(){
    $('.col_' + $(this).val()).hide();
  });*/

  //Init custom checkbox
  $('input[type=checkbox], input[type=radio]').not('label.custom-label input').customRadioCheck();
  
  // Init Top pin state
  if($('.pin :checked').length == $('.pin :checkbox').length) {
    $('.top_gtu_pin :checkbox').attr('checked','checked').trigger('update');
  }
  else {
    $('.top_gtu_pin :checkbox').attr('checked',false).trigger('update');
  }

  
  //Pin a specimen
  $('.gtu_results .pin :checkbox').change(function(){
     pin_gtu($(this).val(), $(this).is(':checked'));
  });

  // Check all pin's on the page
  $('.gtu_results .top_gtu_pin :checkbox').click(function(){
    $('.gtu_results .pin :checkbox').attr('checked', $(this).is(':checked')).trigger('update');
    pins = [];
    $('.gtu_results .pin :checkbox').each(function(){
      pins.push($(this).val());
    });
    pin_gtu(pins, $(this).is(':checked'));
  }); 

  console.log(geosjon);
  
  getFeaturesRow(geosjon);
  
  <?php if($item !==null): ?>
  $("body").on("click",".choose_gtu_in_map",
				function(e)
				{
					console.log("click");
					var id_gtu=$(this).attr("dw_id");
					$("#gtu_choose_<?php print($item->getId());?>").click();
					
				}
			);
			
  <?php endif; ?>
 
  
});
</script>
