  <tbody  class="spec_ident_extlinks_data" id="spec_ident_extlinks_data_<?php echo $rownum;?>">
   <tr class="spec_ident_extlinks_head">
        <th><?php echo __('Url');?></th>
        <th><?php echo __('Comment');?></th>
       
   </tr>
   <tr class="spec_ident_extlinks_data">   
      <td class="top_aligned">
          <?php echo $form['url']->renderError(); ?>
          <?php echo $form['url'];?>
      </td>
      <td>
        <?php echo $form['comment']->renderError(); ?>
        <?php echo $form['comment'];?>
      </td>
  </tr>
  <tr class="spec_ident_extlinks_head">
        <th><?php echo __('Category');?></th>
        <th><?php echo __('Contributor');?></th>
   </tr>
    <tr class="spec_ident_extlinks_data">
      <td>
        <?php echo $form['category']->renderError(); ?>
        <?php echo $form['category'];?>
      </td>    
      <td class="top_aligned">
          <?php echo $form['contributor']->renderError(); ?>
          <?php echo $form['contributor'];?>
      </td>
  </tr>
  <tr class="spec_ident_extlinks_head">
        <th><?php echo __('Disclaimer');?></th>
        <th><?php echo __('License');?></th>
   </tr>
    <tr class="spec_ident_extlinks_data">
     <td>
        <?php echo $form['disclaimer']->renderError(); ?>
        <?php echo $form['disclaimer'];?>
      </td>
      <td>
        <?php echo $form['license']->renderError(); ?>
        <?php echo $form['license'];?>
      </td>
  </tr>
   <tr class="spec_ident_extlinks_head">
        <th><?php echo __('Display order');?></th>
       
   </tr>
  <tr class="spec_ident_extlinks_data">
     <td>
        <?php echo $form['display_order']->renderError(); ?>
        <?php echo $form['display_order'];?>
      </td>
     
  </tr>
      <td class="widget_row_delete">
        <?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_extlinks_'.$rownum); ?>
        <?php echo $form->renderHiddenFields() ?>
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
