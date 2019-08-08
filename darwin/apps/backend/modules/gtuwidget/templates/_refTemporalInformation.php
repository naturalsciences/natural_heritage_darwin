<div id="gtu_temporal_information">    
         <?php $retainedKey = 0;?>
         <?php $existing_key = 0;?>
         <?php $distinct_dates=Array(); ?>
         <?php if($form->getWidgetSchema()->offsetExists('GtuTemporalInformationForm')):?>
            
             <?php foreach($form['GtuTemporalInformationForm'] as $form_value):?>
                <?php $tmp_date=$form_value->getValue() ;?>              
                <?php if(!isset($distinct_dates[$tmp_date['from_date']['year']][$tmp_date['from_date']['month']][$tmp_date['from_date']['day']][$tmp_date['from_date']['hour']][$tmp_date['from_date']['minute']][$tmp_date['from_date']['second']][$tmp_date['to_date']['year']][$tmp_date['to_date']['month']][$tmp_date['to_date']['day']][$tmp_date['to_date']['hour']][$tmp_date['to_date']['minute']][$tmp_date['to_date']['second']][$tmp_date['from_date_mask']][$tmp_date['to_date_mask']]) ):?>
                    <?php $style="display:block"; ?>
                    <?php $distinct_dates[$tmp_date['from_date']['year']][$tmp_date['from_date']['month']][$tmp_date['from_date']['day']][$tmp_date['from_date']['hour']][$tmp_date['from_date']['minute']][$tmp_date['from_date']['second']][$tmp_date['to_date']['year']][$tmp_date['to_date']['month']][$tmp_date['to_date']['day']][$tmp_date['to_date']['hour']][$tmp_date['to_date']['minute']][$tmp_date['to_date']['second']][$tmp_date['from_date_mask']][$tmp_date['to_date_mask']]=true ;?>
                 <?php else: ?>
                    <?php $style="display:none"; ?>
                 <?php endif;?>
                  <div><?php include_partial('gtu/gtu_temporal_information', array('form' => $form_value, 'rownum'=>$retainedKey,"edit"=>"true", "visibility"=> $style));?></div>             
                
                <?php $retainedKey = $retainedKey+1;?>
             <?php endforeach;?>
         <?php endif;?>
         <?php $existing_key=$retainedKey;?>
         <?php if($form->getWidgetSchema()->offsetExists('newGtuTemporalInformationForm')):?>
        
             <?php foreach($form['newGtuTemporalInformationForm'] as $form_value):?>               
               <?php include_partial('gtu/gtu_temporal_information', array('form' => $form_value, 'rownum'=>$retainedKey));?>
                       <?php $retainedKey = $retainedKey+1;?>
             <?php endforeach;?>
         <?php endif;?>
         
   
         
       
 <input type="hidden" id="count_gtu_date" value="0"/>
 </div>
 <div  style="text-align:left" class="add_temporal_information">
            <a href="<?php echo url_for("gtu/AddTemporalInformation/");?>/num/" id="add_temporal_information"><?php echo __('Add Date');?></a>
 </div>
 <script  type="text/javascript">
 $(document).ready(function () {
 
    <?php if($form->getWidgetSchema()->offsetExists('GtuTemporalInformationForm')):?>
        $("#count_gtu_date").val(parseInt("<?php print($existing_key);?>"));       
    <?php endif;?>
    $('#add_temporal_information').click( function(event)
    {
        event.preventDefault();
        hideForRefresh('#gtu_temporal_information');
        parent_el = $('#gtu_temporal_information');
        parentId = $(parent_el).attr('id');
        $.ajax(
        {
          type: "GET",
          url: $(this).attr('href')+ $("#count_gtu_date").val(),
          success: function(html)
          { 
            var lineIndex=parseInt($("#count_gtu_date").val())+1;
            //html="<tr><td>"+html+"</td></tr>";
            //html=html+'<tr class="display_temporal_information_'+lineIndex+'"><td class="display_temporal_information_'+lineIndex+'" ><hr class="display_temporal_information_'+lineIndex+'"></hr></td></tr>';
           
            $( parent_el).append(html);
            showAfterRefresh('#gtu_temporal_information');
          }
        });       
        $("#count_gtu_date").val(parseInt($("#count_gtu_date").val())+1);
        $(this).closest('table.temporal_information').find('thead').show();
        return false;
    }); 
});
</script>