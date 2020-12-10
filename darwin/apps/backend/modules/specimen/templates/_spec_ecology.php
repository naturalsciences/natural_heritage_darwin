  <tbody  class="spec_ident_ecology_data" id="spec_ident_ecology_data_<?php echo $rownum;?>">
   <tr class="spec_ident_ecology_data">
   
      <td>
        <?php echo $form->renderError();?>

        <?php echo $form['comment']->renderError(); ?>
        <?php echo $form['comment'];?>
      </td>
      <td class="widget_row_delete">
        <?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_ecology_'.$rownum); ?>
        <?php echo $form->renderHiddenFields() ?>
      </td>    
    </tr>
   <tr>
     <td colspan="3"><hr /></td>
   </tr>
  </tbody>
  <script type="text/javascript">
    $(document).ready(function () {
      $("#clear_ecology_<?php echo $rownum;?>").click( function()
      {
      
        parent_el = $(this).closest('tbody');
        parentTableId = $(parent_el).closest('table').attr('id');
        $(parent_el).find('textarea').val('');      
            
        $(parent_el).hide();
        visibles = $('table#'+parentTableId+' tbody.spec_ident_ecology_data:visible').size();
        if(!visibles)
        {
          $(this).closest('table#'+parentTableId).find('thead').hide();
        }
      });
    });
  </script>
