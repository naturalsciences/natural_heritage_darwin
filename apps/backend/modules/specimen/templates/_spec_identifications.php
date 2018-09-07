<?php if($form['notion_concerned']->getValue()!=""||
//ftheeten 2018 09 06
sfContext::getInstance()->getActionName()=="addIdentification"||sfContext::getInstance()->getActionName()=="edit"):?>
<tbody class="spec_ident_data" id="spec_ident_data_<?php echo $row_num;?>">
  <?php if($form->hasError()): ?>
  <tr>
    <td colspan="6">
      <?php echo $form->renderError();?>
    </td>
  </tr>
  <?php endif;?>
  <tr class="spec_ident_data">
    <td class="spec_ident_handle">
      <?php echo image_tag('drag.png');?>
    </td>
    <td>
      <?php echo $form['notion_date'];?>
    </td>
    <td>
      <?php echo $form['notion_concerned'];?>
    </td>
    <td>
      <?php echo $form['value_defined'];?>
    </td>
    <td>
      <?php echo $form['determination_status'];?>
    </td>
    <td class="widget_row_delete">
      <?php echo image_tag('remove.png', 'alt=Delete class=clear_identification id=clear_identification_'.$row_num); ?>
      <?php echo $form->renderHiddenFields();?>
    </td>    
  </tr>
  <tr class="spec_ident_identifiers">
    <td></td>
    <td colspan="4">
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
            <td colspan="3">
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
<script  type="text/javascript">
  $(document).ready(function () {
    $("#clear_identification_<?php echo $row_num;?>").click( function()
    {
      parent_el = $(this).closest('tbody');

      $(parent_el).find('input[id$=\"_value_defined\"]').val('');
      $(parent_el).find('input[id$=\"_is_removed\"]').val('');

      $(parent_el).find('select').append("<option value=''></option>").val('');

      $(parent_el).hide();
      reOrderIdent();
      visibles = $('table#identifications tbody.spec_ident_data:visible').size();
      if(!visibles)
      {
        $(this).closest('table#identifications').find('thead.spec_ident_head').hide();
      }
    });

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
      return true;
    }

    $("#spec_ident_identifiers_<?php echo $row_num;?>").catalogue_people({
      add_button: '#add_identifier_<?php echo $row_num ;?>',
      handle: '.spec_ident_identifiers_handle',
      update_row_fct: addIdentifierForIdentification<?php echo $row_num;?>
      });


});
</script></td>
  </tr>
</tbody>
<?php endif;?>
