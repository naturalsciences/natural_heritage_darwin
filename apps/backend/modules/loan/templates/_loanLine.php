<tr class="line_<?php echo $form->getparent()->getName().'_'.$form->getName();?>">
  <td><?php echo $form->renderError();?></td>
  <td>
      <?php echo image_tag('info.png',"title=info class=extd_info");?>

    <?php echo $form['part_ref']->renderError();?>
    <?php echo $form['part_ref'];?>
  </td>
  <td>
    <?php echo $form['ig_ref']->renderError();?>
    <?php echo $form['ig_ref'];?>
  </td>
  <td>
    <?php echo $form['details']->renderError();?>
    <?php echo $form['details'];?>
  </td>
  <td>
    <?php echo $form['from_date']->renderError();?>
    <?php echo $form['from_date'];?>
  </td>
  <td>
    <?php echo $form['to_date']->renderError();?>
    <?php echo $form['to_date'];?>
    <?php echo $form['loan_item_ind'];?>
  </td>
  <td>
    <?php if(! $lineObj->isNew()):?>
      <?php echo link_to(image_tag('edit.png', array("title" => __("Edit"))),'loanitem/edit?id='.$lineObj->getId());?>
    <?php endif;?>
  </td>

  <td> <!--class="widget_row_delete">-->
      <?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_code_'.($lineObj->isNew() ? 'n_' : 'o_').$form->getName()); ?>

<script type="text/javascript">
  $(document).ready(function () {
    $("#clear_code_<?php echo ($lineObj->isNew() ? 'n_' : 'o_').$form->getName();?>").click( function()
    {
      parent_el = $(this).closest('tr');
      parent_el.hide();
      parent_el.find('input[type="hidden"][id$=\"_loan_item_ind\"]').val('');
    });

    bind_ext_line('<?php echo $form->getparent()->getName();?>',  '<?php echo $form->getName();?>')


    $(".line_<?php echo $form->getparent()->getName().'_'.$form->getName();?> [id$=\"_ig_ref_check\"]").change(function(){
      if($(this).val()) 
      {
        $.ajax({
          type: 'POST',
          url: "<?php echo url_for('igs/addNew') ?>",
          data: "num=" + $(".line_<?php echo $form->getparent()->getName().'_'.$form->getName();?> [id$=\"_ig_ref_name\"]").val(),
          success: function(html){
            $(".line_<?php echo $form->getparent()->getName().'_'.$form->getName();?> li#toggledMsg").hide();
            $(".line_<?php echo $form->getparent()->getName().'_'.$form->getName();?> [id$=\"_ig_ref\"]").val(html) ;
          }
        });  
      }
    }) ;
  });
</script>

  </td>
</tr>