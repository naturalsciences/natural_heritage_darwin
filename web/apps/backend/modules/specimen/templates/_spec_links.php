  <tbody  class="spec_ident_extlinks_data" id="spec_ident_extlinks_data_<?php echo $rownum;?>">
   <tr class="spec_ident_extlinks_data">
      <td class="top_aligned">
          <?php echo $form['url']->renderError(); ?>
          <?php echo $form['url'];?>
      </td>
      <td  rowspan="2">
        <?php echo $form['comment']->renderError(); ?>
        <?php echo $form['comment'];?>
      </td>
      <td class="widget_row_delete">
        <?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_extlinks_'.$rownum); ?>
        <?php echo $form->renderHiddenFields() ?>
      </td>    
    </tr>
    <tr>
      <td>
        <strong><?php echo $form['type']->renderLabel(); ?></stong>
        <?php echo $form['type']->renderError(); ?>
        <?php echo $form['type'];?>      
      </td>
   </tr>
   <tr>
     <td colspan="3"><hr /></td>
   </tr>
  </tbody>
  <script type="text/javascript">
    $(document).ready(function () {
      $("#clear_extlinks_<?php echo $rownum;?>").click( function()
      {   
		
	      parent_el = $(this).closest('tbody');
	      parentTableId = $(parent_el).closest('table').attr('id');
	      $(parent_el).find('input[id$=\"_<?php echo $rownum;?>_url\"]').val('');      
       $(parent_el).hide();	  
	      visibles = $('table#'+parentTableId+' tbody.spec_ident_extlinks_data:visible').size();
	      if(!visibles)
	      {
	        $(this).closest('table#'+parentTableId).find('thead').hide();
		    
	      }
      });
    });
  </script>
