<tr class="row_num_<?php echo $row_num;?>">
  <td>
    <?php echo $form['title']->renderError() ?>
    <?php echo $form['title']->render() ; ?>
  </td>
  <td><?php echo $form['description']->render() ; ?></td>
  <td><?php $date = new DateTime($form['creation_date']->getValue());
                echo $date->format('d/m/Y'); ?></td>
  <td>
    <?php if($table!='loans' && $table!='loan_items'):?>
      <?php echo $form['visible']->render() ; ?>
      <script tpe="text/javascript">
        $("input#<?php echo $form['visible']->renderId();?>").change(function () {
          if (!($(this).attr('checked'))) {
            $("input#<?php echo $form['publishable']->renderId();?>").attr('checked', false);
          }
        });
      </script>
    <?php endif;?>
  </td>
  <td>
    <?php if($table!='loans' && $table!='loan_items'):?>
      <?php echo $form['publishable']->render() ; ?>
      <script tpe="text/javascript">
        $("input#<?php echo $form['publishable']->renderId();?>").change(function () {
          if ($(this).attr('checked')) {
            $("input#<?php echo $form['visible']->renderId();?>").attr('checked', true);
          }
        });
      </script>
    <?php endif;?>
  </td>
  <td class="widget_row_delete" rowspan="2">
    <?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_file_'.$row_num); ?>
    <?php echo $form->renderHiddenFields();?>
    <script type="text/javascript">
      $("#clear_file_<?php echo $row_num;?>").click( function()
      {
        parent_el = $(this).closest('tbody');
        parent_tr = $(parent_el).find('tr.row_num_<?php echo $row_num;?>');
        $(parent_tr).find('input').val('');
        $(parent_tr).hide();
        visibles = $(parent_el).find('tr:visible').size();
        if(visibles==0)
        {
          $(this).closest('table.related_files').find('thead').hide();
        }
      });
    </script>
  </td>
</tr>
<tr class="row_num_<?php echo $row_num;?>">
  <td>
    <?php if(isset($edit)) : ?>
     
        <?php echo link_to($form['filename']->getValue()." ".image_tag('criteria.png'),
            'multimedia/downloadFile?id='.$form['id']->getValue()) ; ?>

    <?php else : ?>
      <?php echo $form['filename']->getValue(); ?>
    <?php endif ; ?>
  </td>
  <td colspan="4"><?php echo $form['mime_type']->getValue() ; ?>
   <?php if(isset($object) && $object):?> (<?php echo $object->getHumanSize();?>)<?php endif; ?>
   </td>
</tr>
<script type="text/javascript">
  $("tr.row_num_<?php echo $row_num;?>").hover(function(){
                                                          parent_el = $(this).closest('tbody');
                                                          parent_tr = $(parent_el).children('tr.row_num_<?php echo $row_num;?>');
                                                          $(parent_tr).css('background-color', '#E9EDBE');
                                                         },
                                               function(){
                                                          parent_el = $(this).closest('tbody');
                                                          parent_tr = $(parent_el).children('tr.row_num_<?php echo $row_num;?>');
                                                          $(parent_tr).css('background-color', '#F6F6F6');
                                                         });
</script>
