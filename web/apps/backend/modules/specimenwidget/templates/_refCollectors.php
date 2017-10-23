<?php echo javascript_include_tag('catalogue_people.js') ?>
 <table class="property_values collectors" id="spec_ident_collector">
   <thead style="<?php echo ($form['Collectors']->count() || $form['newCollectors']->count())?'':'display: none;';?>" class="spec_ident_collector_head">
	<tr>
	   <th colspan='3'>
		   <?php echo $form['Collectors_holder'];?>
	   </th>
	</tr>
   </thead>
   <tbody id="spec_ident_collector_body">
     <?php $retainedKey = 0;?>
     <?php foreach($form['Collectors'] as $form_value):?>   
       <?php include_partial('specimen/spec_people_associations', array('type'=> 'collector','form' => $form_value, 'row_num'=>$retainedKey));?>
       <?php $retainedKey = $retainedKey+1;?>
     <?php endforeach;?>
     <?php foreach($form['newCollectors'] as $form_value):?>
       <?php include_partial('specimen/spec_people_associations', array('type'=> 'collector','form' => $form_value, 'row_num'=>$retainedKey));?>
       <?php $retainedKey = $retainedKey+1;?>
     <?php endforeach;?>
   </tbody>     
   <tfoot>
     <tr>
       <td colspan="3">
         <div class="add_code">
           <a href="<?php echo url_for('specimen/AddCollector'.($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>/num/" class="hidden"></a>
           <a class='add_collector' href="<?php echo url_for('people/searchBoth?with_js=1');?>"><?php echo __('Add collector');?></a>
         </div>
       </td>
     </tr>
   </tfoot>
 </table>
	

<script  type="text/javascript">
$(document).ready(function () {


function addCollector(people_ref, people_name)
{ 
  info = 'ok';
  $('#spec_ident_collector tbody tr').each(function() {
    if($(this).find('input[id$=\"_people_ref\"]').val() == people_ref) info = 'bad' ;
  });
  if(info != 'ok') return false;

  hideForRefresh($('.ui-tooltip-content .page')) ; 
  $.ajax(
  {
    type: "GET",
    url: $('#spec_ident_collector .add_code a.hidden').attr('href')+ (0+$('#spec_ident_collector tbody tr').length)+'/people_ref/'+people_ref + '/iorder_by/' + (0+$('#spec_ident_collector tbody tr').length),
    success: function(html)
    {
      $('#spec_ident_collector tbody').append(html);
      $.fn.catalogue_people.reorder($('#spec_ident_collector'));
      showAfterRefresh($('.ui-tooltip-content .page')) ; 
	  
	  //ftheeten 2016 02 24
	  position_to_Scroll=$('#refCollectors').offset().top;
	 $('body').trigger('close_modal');
	  $('body').parent().scrollTop(position_to_Scroll);
    }
  });
  return true;
}

$("#spec_ident_collector").catalogue_people({handle: '.spec_ident_collector_handle', add_button: '#spec_ident_collector a.add_collector', q_tip_text: 'Choose a Collector',update_row_fct: addCollector });


});

</script>
