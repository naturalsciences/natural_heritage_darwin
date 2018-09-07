    <tr class="<?php echo $type;?>_data" id="<?php echo $type;?>_<?php echo $row_num; ?>">
      <td><?php echo image_tag('drag.png','class='.$type.'_table_handle');?></td>
      <td>
        <?php echo image_tag('info-green.png',"title=info class=".$type."_extd_info_$row_num");?>
        <div class="extended_info" style="display:none;">          
        </div>
        <script  type="text/javascript">
          $(".<?php echo $type;?>_extd_info_<?php echo $row_num;?>").qtip({
            show: { solo: true, event:'mouseover' },
            hide: { event:'mouseout' },
            style: 'ui-tooltip-light ui-tooltip-rounded ui-tooltip-dialogue',
            content: {
              text: '<img src="/images/loader.gif" alt="loading"> Loading ...',
              title: { text: '<?php echo __("People Info") ; ?>' },
              ajax: {
                url: '<?php echo url_for("people/extendedInfo");?>',
                type: 'GET',
                data: { id: '<?php echo $form["people_ref"]->getValue() ; ?>' }
              }
            }
          });        
        </script>
      </td>
      <td><?php if($form['people_ref']->getValue()) : ?>
      <?php echo image_tag(Doctrine::getTable('People')->find($form['people_ref']->getValue())
                               ->getCorrespondingImage()) ; ?>
          <?php endif ; ?>
      </td>
      <td><?php
                $is_physical = Doctrine::getTable('People')->find($form['people_ref']->getValue())->getIsPhysical();
                echo link_to($form['people_ref']->renderLabel(),
                             (($is_physical)?'people':'institution').'/edit',
                             array(
                               'query_string' => 'id='.$form['people_ref']->getValue()
                             )
        );?></td>
      <?php echo $form['people_sub_type']->render() ; ?>
      <td class="widget_row_delete">
        <?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_'.$type.'_'.$row_num); ?>
        <?php echo $form->renderHiddenFields();?>
    <script type="text/javascript">
      $(document).ready(function () {
        $("#clear_<?php echo $type;?>_<?php echo $row_num;?>").click( function()
        {
           parent_el = $(this).closest('tr');
           parentTableId = $(parent_el).closest('table').attr('id')
           $(parent_el).find('input[id$=\"_people_ref\"]').val('');
           $(parent_el).hide();
           $.fn.catalogue_people.reorder( $(parent_el).closest('table') );
           visibles = $('table#'+parentTableId+' .<?php echo $type;?>_data:visible').size();
        });
        $('table .hidden_record').each(function() {
          $(this).closest('tr').hide() ;
        });
      });
    </script>
    </td>
  </tr>
