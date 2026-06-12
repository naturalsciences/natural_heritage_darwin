<?php if($form['notion_concerned']->getValue()!=""||
//ftheeten 2018 09 06
sfContext::getInstance()->getActionName()=="addIdentification"||sfContext::getInstance()->getActionName()=="edit"):?>
 <tbody class="spec_ident_data" id="spec_ident_data_<?php echo $row_num;?>" class="spec_ident_data_<?php echo $row_num;?>">
  <?php if($form->hasError()): ?>
  
  <tr>
    <td>
      <?php echo $form->renderError();?>
    </td>
  </tr>
  <?php endif;?>
  <tr>
	<td class="widget_row_delete" colspan="4">
      <?php echo image_tag('remove.png', 'alt=Delete class=clear_identification id=clear_identification_'.$row_num.' id_row='.$row_num); ?>
      <?php echo $form->renderHiddenFields();?>
    </td>  
  </tr>
<?php if($row_num>0): ?>
  
    <tr>
      <th></th>
      <th><?php echo __('Date'); ?></th>
      <th><?php echo __('Category');?></th>
      <th><?php echo __('Subject'); ?></th>      
    </tr>

 <?php else:?>

 <?php endif;?>
 
  <tr class="spec_ident_data">
    <td >
      
    </td>
    <td style="white-space:nowrap;">
      <?php echo $form['notion_date'];?>
    </td>
    <td>
      <?php echo $form['notion_concerned'];?>
    </td>
    <td>
      <?php echo $form['value_defined'];?>
    </td>
	</tr>
	<tr>
		<th style="padding-top:10px" ></th>
		<th colspan="2" style="padding-top:10px">
		  <?php echo __('Det. St.'); ?>
		</td>
	<tr>
	<tr >
		<td></td>
		<td colspan="2">
		  <?php echo $form['determination_status'];?>
		</td>
     
  </tr>
 
  <tr class="spec_ident_identifiers">
    <td></td>
    <td colspan="5">
      <?php $borderClass = (!$form['Identifiers']->count() && !$form['newIdentifier']->count())?'':'green_border';?>
      <table class="property_values identifiers <?php echo $borderClass;?>" id="spec_ident_identifiers_<?php echo $row_num;?>">
        <thead style="<?php echo ($form['Identifiers']->count() || $form['newIdentifier']->count())?'':'display: none;';?>" class="spec_ident_identifiers_head">
         <tr>
            <td colspan="3"><?php echo __('Identifiers');?></td>
          </tr>
        </thead>
        <tbody>
        <?php $retainedKey = 0;?>
        <?php foreach($form['Identifiers'] as $form_value):?>
          <?php include_partial('specimen/spec_identification_identifiers', array('form' => $form_value, 'rownum'=>$retainedKey, 'identnum' => $row_num));?>
          <?php $retainedKey = $retainedKey+1;?>
        <?php endforeach;?>
        <?php foreach($form['newIdentifier'] as $form_value):?>
          <?php include_partial('specimen/spec_identification_identifiers', array('form' => $form_value, 'rownum'=>$retainedKey, 'identnum' => $row_num));?>
          <?php $retainedKey = $retainedKey+1;?>
        <?php endforeach;?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" style="text-align:left;">
              <div class="add_code">
                <a href="<?php echo url_for($module.'/addIdentifier?spec_id='.($spec_id?$spec_id:'0').(($individual_id ==0 ) ? '': '&individual_id='.$individual_id).((!isset($identification_id))?'':'&identification_id='.$identification_id)).'/num/'.$row_num;?>/identifier_num/" class="hidden"></a>
                <a id="add_identifier_<?php echo $row_num ;?>" href="<?php echo url_for('people/choose?with_js=1');?>"><?php echo __('Add identifier');?></a>              
              </div>
            </td>
          </tr>
        </tfoot>
      </table>
    </td>
    <td>
   </td>
  </tr>
     <tr class="spec_ident_identifiers"  >
	<td colspan="4"><input type="button" class="show_hide_ident_count" value="Show/hide count" id_row="<?php echo $row_num ;?>" ></input>

	
	 <?php $val_count_min=$form['identifications_count_min']->getValue() ?>
   <?php $mode_visibility_count=test_is_set('identifications_count_min', $form); ?>
   <?php if($is_new):?>
		<?php  $mode_display_count="display:none";?>  
   <?php elseif($mode_visibility_count && (!$is_new)):?>
		<?php  $mode_display_count="";?>  
	<?php else:?>
		<?php  $mode_display_count="display:none";?>  
	 <?php endif;?>	
   </td>
  </tr>
  </tbody>  
  
 
  <tbody id="spec_ident_data_counter_<?php echo $row_num;?>">
  <tr class="sex_Top_Bordered toggle_count_ident " class=""  id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
   	 <th></th>
	 <th colspan="2"><B><u>Total</u></B></th>
  </tr>
  <?php $accuracy_default=test_precise_imprecise('accuracy', 'identifications_count_min',  'identifications_count_max', $form);?> 
  <tr class="sex_Middle_Bordered toggle_count_ident "   id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
	<th></th>
	<th class="top_aligned"><?php echo $form['accuracy']->renderLabel();?></th>
	<td  >
	  <?php echo $form['accuracy']->renderError();?>
	  <?php echo $form['accuracy']->render(array("id_row"=>$row_num)); ?>
	</td>
  </tr>
 
  <tr class="sex_Middle_Bordered toggle_count_ident" id='identifications_count_min'  id_row="<?php echo $row_num ;?>"  style="<?php  print($mode_display_count);?>">
	<th></th>
	<th  ><?php echo $form['identifications_count_min']->renderLabel();?></th>
	<td  colspan="2">
	  <?php echo $form['identifications_count_min']->renderError();?>
	  <?php echo $form['identifications_count_min']->render(array("id_row"=>$row_num)) ?>
	</td>
  </tr>
  <tr class="sex_Middle_Bordered toggle_count_ident identifications_count_max ident_counter_max"  id='identifications_count_max' id_row="<?php echo $row_num ;?>" >
	<th></th>
	<th><?php echo $form['identifications_count_max']->renderLabel();?></th>
	<td colspan="2">
	  <?php echo $form['identifications_count_max']->renderError();?>
	  <?php echo $form['identifications_count_max']->render(array("id_row"=>$row_num)) ?>
	</td>
  </tr>
  <tr class="sex_Bottom_Bordered toggle_count_ident " id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
	<td></td>
	<td colspan="2"></td>
  </tr>

  <!--ftheeten 2016 06 22-->
  <tr class="sex_Top_Bordered toggle_count_ident"  id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
   	 <th></th>
	 <th colspan="2"><B><u>Males</u></B></th>
  </tr>
  <tr class="sex_Middle_Bordered toggle_count_ident" id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
    <th></th>
	<th class="top_aligned"><?php echo $form['accuracy_males']->renderLabel();?></th>
	<td>
	  <?php echo $form['accuracy_males']->renderError();?>
	  <?php echo $form['accuracy_males']->render(array("id_row"=>$row_num)) ?>
	</td>
  </tr>
  <tr class="sex_Middle_Bordered toggle_count_ident" id='identifications_count_min'  id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
    <th></th>
	<th ><?php echo $form['identifications_count_males_min']->renderLabel();?></th>
	<td colspan="2">
	  <?php echo $form['identifications_count_males_min']->renderError();?>
	  <?php echo $form['identifications_count_males_min']->render(array("id_row"=>$row_num)) ?> &#9794;
	</td>
  </tr>
  <tr  class="sex_Middle_Bordered toggle_count_ident" id='identifications_count_max' id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
    <th></th>
	<th ><?php echo $form['identifications_count_males_max']->renderLabel();?></th>
	<td colspan="2">
	  <?php echo $form['identifications_count_males_max']->renderError();?>
	  <?php echo $form['identifications_count_males_max']->render(array("id_row"=>$row_num)) ?> &#9794;
	</td>
  </tr>
  <tr class="sex_Bottom_Bordered toggle_count_ident"  id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
	<td></td>
	<td colspan="2"></td>
  </tr>
  <!--ftheeten 2016 06 22-->
  <tr class="sex_Top_Bordered toggle_count_ident" id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
   	<th></th>
	<th colspan=2><B><u>Females</u></B></th>
  </tr>
  <tr class="sex_Middle_Bordered toggle_count_ident" id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
	<th></th>
	<th class="top_aligned" ><?php echo $form['accuracy_females']->renderLabel();?></th>
	<td >
	  <?php echo $form['accuracy_females']->renderError();?>
	  <?php echo $form['accuracy_females']->render(array("id_row"=>$row_num)) ?>
	</td>
  </tr>
  <tr class="sex_Middle_Bordered toggle_count_ident" id='identifications_count_min' id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
	<th></th>
	<th  ><?php echo $form['identifications_count_females_min']->renderLabel();?></th>
	<td colspan="2" >
	  <?php echo $form['identifications_count_females_min']->renderError();?>
	  <?php echo $form['identifications_count_females_min']->render(array("id_row"=>$row_num)) ?> &#9792;
	</td>
  </tr>
  <tr class="sex_Middle_Bordered toggle_count_ident" id='identifications_count_max'  id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
	<th></th>
	<th ><?php echo $form['identifications_count_females_max']->renderLabel();?></th>
	<td colspan="2" >
	  <?php echo $form['identifications_count_females_max']->renderError();?>
	  <?php echo $form['identifications_count_females_max']->render(array("id_row"=>$row_num)) ?> &#9792;
	</td >
  </tr>
  <tr class="sex_Bottom_Bordered toggle_count_ident" id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
	<td></td>
	<td colspan="2"></td>
  </tr>
  <!--ftheeten 2016 06 22-->
  <tr class="sex_Top_Bordered toggle_count_ident"  id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
   	<th></th>
	<th colspan=2><B><u>Juveniles</u></B></th>
  </tr>
  <tr class="sex_Middle_Bordered toggle_count_ident" id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
	<th></th>
	<th class="top_aligned"><?php echo $form['accuracy_juveniles']->renderLabel();?></th>
	<td >
	  <?php echo $form['accuracy_juveniles']->renderError();?>
	  <?php echo $form['accuracy_juveniles']->render(array("id_row"=>$row_num)) ?>
	</td>
  </tr>
  <tr class="sex_Middle_Bordered toggle_count_ident" id='identifications_count_min' id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
	<th></th>
	<th><?php echo $form['identifications_count_juveniles_min']->renderLabel();?></th>
	<td colspan="2">
	  <?php echo $form['identifications_count_juveniles_min']->renderError();?>
	  <?php echo $form['identifications_count_juveniles_min']->render(array("id_row"=>$row_num)) ?> Juv.
	</td>
  </tr>
  <tr class="sex_Middle_Bordered toggle_count_ident" id='identifications_count_max'  id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
    <th></th>
	<th><?php echo $form['identifications_count_juveniles_max']->renderLabel();?></th>
	<td colspan="2">
	  <?php echo $form['identifications_count_juveniles_max']->renderError();?>
	  <?php echo $form['identifications_count_juveniles_max']->render(array("id_row"=>$row_num)) ?> Juv.
	</td>
  </tr>
  <tr class="sex_Top_Bordered toggle_count_ident"  id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
   	<th></th>
	<th colspan=2><B><u>Types</u></B></th>
  </tr>
  <tr class="sex_Middle_Bordered toggle_count_ident" id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
	<th></th>
	<th class="top_aligned"><?php echo $form['accuracy_types']->renderLabel();?></th>
	<td >
	  <?php echo $form['accuracy_types']->renderError();?>
	  <?php echo $form['accuracy_types']->render(array("id_row"=>$row_num)) ?>
	</td>
  </tr>
  
   <tr class="sex_Middle_Bordered toggle_count_ident" id='identifications_count_min' id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
	<th></th>
	<th><?php echo $form['identifications_count_types_min']->renderLabel();?></th>
	<td colspan="2">
	  <?php echo $form['identifications_count_types_min']->renderError();?>
	  <?php echo $form['identifications_count_types_min']->render(array("id_row"=>$row_num)) ?> Types
	</td>
  </tr>
  <tr class="sex_Middle_Bordered toggle_count_ident" id='identifications_count_max'  id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
    <th></th>
	<th><?php echo $form['identifications_count_types_max']->renderLabel();?></th>
	<td colspan="2">
	  <?php echo $form['identifications_count_types_max']->renderError();?>
	  <?php echo $form['identifications_count_types_max']->render(array("id_row"=>$row_num)) ?> Types
	</td>
  </tr>
  <tr class="sex_Bottom_Bordered toggle_count_ident" id_row="<?php echo $row_num ;?>" style="<?php  print($mode_display_count);?>">
	<td></td>
	<td colspan=2></td>
  </tr>
</tbody>
<tr>
	<td colspan="4" style="padding-top=10px;"><hr/></td>
  <tr>
<script  type="text/javascript">
  $(document).ready(function () {
   

    function addIdentifierForIdentification<?php echo $row_num;?>(people_ref, people_name)
    {
      info = 'ok';
      $('#spec_ident_identifiers_<?php echo $row_num;?> tbody tr').each(function() {
        if($(this).find('input[id$=\"_people_ref\"]').val() == people_ref) info = 'bad' ;
      });
      if(info != 'ok') return false;
      hideForRefresh($('.ui-tooltip-content .page')) ; 
      $.ajax({
        type: "GET",
        url: $('a#add_identifier_<?php echo $row_num;?>').prev('a.hidden').attr('href')+ (0+$('#spec_ident_identifiers_<?php echo $row_num;?> tbody tr').length)+'/people_ref/'+people_ref + '/iorder_by/' + (0+$('#spec_ident_identifiers_<?php echo $row_num;?> tbody tr').length),
        success: function(html)
        {
          $('table#identifications #spec_ident_identifiers_<?php echo $row_num;?> tbody').append(html);
          $.fn.catalogue_people.reorder($('#spec_ident_identifiers_<?php echo $row_num;?>'));
          $('table#identifications #spec_ident_identifiers_<?php echo $row_num;?> thead').show();
          $('table#identifications #spec_ident_identifiers_<?php echo $row_num;?>').addClass('green_border');
	  showAfterRefresh($('.ui-tooltip-content .page')) ; 
        }
      });
      
      //ftheeten 2016 02 24

	  position_to_Scroll=$('#refIdentifications').offset().top;
	 $('body').trigger('close_modal');
	  $('body').parent().scrollTop(position_to_Scroll);
      return true;
    }

    $("#spec_ident_identifiers_<?php echo $row_num;?>").catalogue_people({
      add_button: '#add_identifier_<?php echo $row_num ;?>',
      handle: '.spec_ident_identifiers_handle',
      update_row_fct: addIdentifierForIdentification<?php echo $row_num;?>
      });


});
</script>

<?php endif;?>
