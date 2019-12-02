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
        <?php echo image_tag('remove.png', 'alt=Delete class="clear_code clear_link" id=clear_extlinks_'.$rownum); ?>
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