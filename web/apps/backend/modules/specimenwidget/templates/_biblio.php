<?php echo javascript_include_tag('catalogue_people.js') ?>
 <table class="property_values biblios" id="spec_ident_biblio">
   <thead style="<?php echo ($form['Biblio']->count() || $form['newBiblio']->count()) ? '' : 'display: none;';?>" class="spec_ident_biblio_head">
	<tr>
	   <th colspan='3'>
		   <?php echo $form['Biblio_holder'];?>
	   </th>
	</tr>
   </thead>
   <tbody id="spec_ident_biblio_body">
     <?php $retainedKey = 0;?>
     <?php foreach($form['Biblio'] as $form_value):?>   
       <?php include_partial('specimen/biblio_associations', array('form' => $form_value, 'row_num'=>$retainedKey));?>
       <?php $retainedKey = $retainedKey+1;?>
     <?php endforeach;?>
     <?php foreach($form['newBiblio'] as $form_value):?>
       <?php include_partial('specimen/biblio_associations', array('form' => $form_value, 'row_num'=>$retainedKey));?>
       <?php $retainedKey = $retainedKey+1;?>
     <?php endforeach;?>
   </tbody>     
   <tfoot>
     <tr>
       <td colspan="3">
         <div class="add_code">
           <a href="<?php echo url_for($module.'/AddBiblio'.($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>/num/" class="hidden"></a>
           <a class='add_biblio' href="<?php echo url_for('bibliography/choose?with_js=1');?>"><?php echo __('Add bibliography');?></a>
         </div>
       </td>
     </tr>
   </tfoot>
 </table>
	

<script  type="text/javascript">
$(document).ready(function () {


function addBibliography(bib_ref, bib_name)
{ 
  info = 'ok';
  $('#spec_ident_biblio tbody tr').each(function() {
    if($(this).find('input[id$=\"_bibliography_ref\"]').val() == bib_ref) info = 'bad' ;
  });
  if(info != 'ok') return false;

  hideForRefresh($('.ui-tooltip-content .page')) ; 
  $.ajax(
  {
    type: "GET",
    url: $('#spec_ident_biblio .add_code a.hidden').attr('href')+ (0+$('#spec_ident_biblio tbody tr').length)+'/biblio_ref/'+bib_ref + '/iorder_by/' + (0+$('#spec_ident_biblio tbody tr').length),
    success: function(html)
    {
      $('#spec_ident_biblio_body').append(html);
      showAfterRefresh($('.ui-tooltip-content .page')) ; 
    }
  });
  return true;
}

$("#spec_ident_biblio").catalogue_people({handle: '', add_button: '#spec_ident_biblio a.add_biblio', q_tip_text: 'Choose a Bibliography',update_row_fct: addBibliography });


});

</script>
