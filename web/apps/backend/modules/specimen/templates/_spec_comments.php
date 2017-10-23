  <tbody  class="spec_ident_comments_data" id="spec_ident_comments_data_<?php echo $rownum;?>">
   <tr class="spec_ident_comments_data">
      <td class="top_aligned">
          <?php echo $form['notion_concerned']->renderError(); ?>
          <?php echo $form['notion_concerned'];?>
      </td>
      <td>
        <?php echo $form->renderError();?>

        <?php echo $form['comment']->renderError(); ?>
        <?php echo $form['comment'];?>
      </td>
      <td class="widget_row_delete">
        <?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_comment_'.$rownum); ?>
        <?php echo $form->renderHiddenFields() ?>
      </td>    
    </tr>
   <tr>
     <td colspan="3"><hr /></td>
   </tr>
  </tbody>
  <script type="text/javascript">
    $(document).ready(function () {
      $("#clear_comment_<?php echo $rownum;?>").click( function()
      {
        parent_el = $(this).closest('tbody');
        parentTableId = $(parent_el).closest('table').attr('id');
        $(parent_el).find('textarea[id$=\"_comment\"]').val('');      
        $(parent_el).find('select[id$=\"_notion_concerned\"]').val('');         
        $(parent_el).hide();
        visibles = $('table#'+parentTableId+' tbody.spec_ident_comments_data:visible').size();
        if(!visibles)
        {
          $(this).closest('table#'+parentTableId).find('thead').hide();
        }
      });
    });
  </script>
