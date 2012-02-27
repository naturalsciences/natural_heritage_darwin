<tr class="line_<?php echo $form->getparent()->getName().'_'.$form->getName();?>">
  <td>
    <?php echo $form->renderError();?>
    <?php if(!$lineObj->isNew()):?>
      <input value="<?php echo $lineObj->getId();?>" type="checkbox" class="select_chk_box" />
    <?php endif;?>
  </td>
  <td>
      <?php echo image_tag('info.png',"title=info class=extd_info");?>
    <?php echo $form['part_ref']->renderError();?>
    <?php echo $form['part_ref'];?>
  </td>
  <td>
    <?php echo $form['ig_ref']->renderError();?>
    <?php echo $form['ig_ref'];?>
  </td>
  <td rowspan="2">
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
    <?php echo $form['item_visible'];?>
    <?php if(! $lineObj->isNew()):?>
      <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'loanitem/view?id='.$lineObj->getId());?>
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
      parent_el.next().hide();
      parent_el.next().next().hide();
      parent_el.find('input[type="hidden"][id$=\"_item_visible\"]').val('');
      if($('.loan_overview_form > table tbody > tr:visible').length ==0){
        $('.loan_overview_form > table').addClass('hidden');
         $('.warn_message').removeClass('hidden');
      }
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
    });
    <?php if(!$lineObj->isNew()):?>

    $(".main_but_line_<?php echo $form->getparent()->getName().'_'.$form->getName();?> .maint_butt").click(function(){
      but_link = $(this);
      el = $(".maint_line_<?php echo $form->getparent()->getName().'_'.$form->getName();?> .maintenance_details");
      if(! el.is(':visible')) {
        $.ajax({
          url: "<?php echo url_for('loanitem/showmaintenances');?>",
          data: { id: <?php echo $lineObj->getId();?> },
          success: function(html){
            $(el).html(html);
            el.show();
            but_link.find('img').attr('src','<?php echo url_for('/images/blue_expand_up.png');?>');
          }
        });

      } else {
        el.hide();
        $(this).find('img').attr('src','<?php echo url_for('/images/blue_expand.png');?>');
      }
    });
    <?php endif;?>
  });
</script>

  </td>
</tr>
<tr class="main_but_line_<?php echo $form->getparent()->getName().'_'.$form->getName();?>">
  <td></td>
  <td></td>
  <td colspan="2">
    <a class="maint_butt<?php if($lineObj->isNew()) echo 'disabled';?>" href="#">
      <?php echo image_tag( ($lineObj->isNew() ? 'grey' : 'blue' ).'_expand.png');?> <?php echo __('Maintenances');?>
    </a>
  </td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
</tr>
<tr class="maint_line_<?php echo $form->getparent()->getName().'_'.$form->getName();?>">
  <td></td>
  <td colspan="6"><div class="maintenance_details"></div>
  </td>
  <td></td>
</tr>

