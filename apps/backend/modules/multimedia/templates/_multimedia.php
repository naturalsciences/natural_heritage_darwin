<?php if($form['referenced_relation']->getValue()!=""):?>
<tr class="row_num_<?php echo $row_num;?>">
  <td>
    <?php echo $form['title']->renderError() ?>
    <?php echo $form['title']->render() ; ?>
  </td>
  <td><?php echo $form['description']->render() ; ?></td>
  <td><?php $date = new DateTime($form['creation_date']->getValue());
                echo $date->format('d/m/Y'); ?></td>
  <td><?php echo $form['visible']->render() ; ?></td>
  <td><?php echo $form['publishable']->render() ; ?></td>
  <td class="widget_row_delete" rowspan="2">
    <?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_file_'.$row_num); ?>
    <?php echo $form->renderHiddenFields();?>
    <script type="text/javascript">
      $("#clear_file_<?php echo $row_num;?>").click( function()
      {
        parent_el = $(this).closest('tbody');
        parent_tr = $(parent_el).find('tr.row_num_<?php echo $row_num;?>');
        $(parent_tr).find('input[id$=\"_referenced_relation\"]').val('');
        $(parent_tr).hide();
        visibles = $(parent_el).find('tr:visible').size();
        if(visibles==0)
        {
          $(this).closest('table.related_files').find('thead').hide();
        }
      });
      $('.uploadfield_<?php echo $row_num ; ?>').bind('change', function() {
        $(this).closest('.divFile').find('.inputText').val($(this).val());
      });
    </script>
  </td>
</tr>
<tr class="row_num_<?php echo $row_num;?>">
  <td>
    <?php if(isset($edit)) : ?>
      <?php  /*If image => preview*/ if(in_array($form['mime_type']->getValue() ,array('png' => 'image/png', 'jpg' => 'image/jpeg') ) ):?>
        <a href="<?php echo url_for( 'multimedia/downloadFile?id='.$form['id']->getValue());?>"><img src="<?php echo url_for('multimedia/preview?id='.$form['id']->getValue());?>" width="100" /></a>
      <?php else:?>
        <?php echo link_to($form['filename']->getValue()." ".image_tag('criteria.png'),
            'multimedia/downloadFile?id='.$form['id']->getValue()) ; ?>
      <?php endif;?>
    <?php else : ?>
      <?php echo $form['filename']->getValue(); ?>
    <?php endif ; ?>
  </td>
  <td colspan="4"><?php echo $form['mime_type']->getValue() ; ?></td>
</tr>
<?php endif; ?>
