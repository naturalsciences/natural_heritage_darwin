<?php if($form['referenced_relation']->getValue()!=""):?>
<tr>
  <td>
    <?php echo $form['title']->renderError() ?>
    <?php echo $form['title']->render() ; ?>
  </td>
  <td><?php echo $form['description']->render() ; ?></td>  
  <td>
    <?php if(isset($edit)) : ?>
      <?php echo link_to($form['filename']->getValue()." ".image_tag('criteria.png'),
                         'multimedia/downloadFile?id='.$form['id']->getValue()) ; ?>
    <?php else : ?>
      <?php echo $form['filename']->getValue(); ?>
    <?php endif ; ?>
  </td>
  <td><?php echo $form['mime_type']->getValue() ; ?></td>
  <td><?php $date = new DateTime($form['creation_date']->getValue());
                echo $date->format('d/m/Y'); ?></td>
  <td class="widget_row_delete">
    <?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_file_'.$row_num); ?>
    <?php echo $form->renderHiddenFields();?>
    <script type="text/javascript">
      $("#clear_file_<?php echo $row_num;?>").click( function()
      {
        parent_el = $(this).closest('tbody');
        parent_tr = $(this).closest('tr');
        $(parent_tr).find('input[id$=\"_referenced_relation\"]').val('');
        $(parent_tr).hide(); 
        visibles = $(parent_el).find('tr:visible').size();
        if(!visibles)
        {
          $(this).closest('table.property_values').find('thead').hide();
        }
      });
      $('.uploadfield_<?php echo $row_num ; ?>').bind('change', function() {
        $(this).closest('.divFile').find('.inputText').val($(this).val());
      });
    </script>
  </td> 
</tr>
<?php endif; ?>
