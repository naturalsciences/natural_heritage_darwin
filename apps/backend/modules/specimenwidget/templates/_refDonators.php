<?php echo javascript_include_tag('catalogue_people.js') ?>
 <table class="property_values donators" id="spec_ident_donator">
   <thead style="<?php echo ($form['Donators']->count() || $form['newDonators']->count())?'':'display: none;';?>" class="spec_ident_donator_head">
  <tr>
     <th colspan='3'>
       <?php echo $form['Donators_holder'];?>
     </th>
  </tr>
   </thead>
   <tbody id="spec_ident_donator_body">
     <?php $retainedKey = 0;?>
     <?php foreach($form['Donators'] as $form_value):?>   
       <?php include_partial('specimen/spec_people_associations', array('type'=> 'donator','form' => $form_value, 'row_num'=>$retainedKey));?>
       <?php $retainedKey = $retainedKey+1;?>
     <?php endforeach;?>
     <?php foreach($form['newDonators'] as $form_value):?>
       <?php include_partial('specimen/spec_people_associations', array('type'=> 'donator','form' => $form_value, 'row_num'=>$retainedKey));?>
       <?php $retainedKey = $retainedKey+1;?>
     <?php endforeach;?>
   </tbody>
   <tfoot>
     <tr>
       <td colspan="3">
         <div class="add_code">
           <a href="<?php echo url_for('specimen/AddDonator'.($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>/num/" class="hidden"></a>
           <a class='add_donator' href="<?php echo url_for('people/searchBoth?&with_js=1');?>"><?php echo __('Add Donator or Seller');?></a>
         </div>
       </td>
     </tr>
   </tfoot>
 </table>
  

<script  type="text/javascript">
$(document).ready(function () {


function addDonator(people_ref, people_name)
{ 

  info = 'ok';
  $('#spec_ident_donator tbody tr').each(function() {
    if($(this).find('input[id$=\"_people_ref\"]').val() == people_ref) info = 'bad' ;
  });
  if(info != 'ok') return false;

  hideForRefresh($('.ui-tooltip-content .page')) ; 
  $.ajax(
  {
    type: "GET",
    url: $('#spec_ident_donator .add_code a.hidden').attr('href')+ (0+$('#spec_ident_donator tbody tr').length)+'/people_ref/'+people_ref + '/iorder_by/' + (0+$('#spec_ident_donator tbody tr').length),
    success: function(html)
    {
      $('#spec_ident_donator tbody').append(html);
      $.fn.catalogue_people.reorder($('#spec_ident_donator'));
      showAfterRefresh($('.ui-tooltip-content .page')) ; 
    }
  });
  return true;
}

$("#spec_ident_donator").catalogue_people({ handle: '.spec_ident_donator_handle', add_button: '#spec_ident_donator a.add_donator', q_tip_text: 'Choose a Donator or Seller',update_row_fct: addDonator });


});

</script>
