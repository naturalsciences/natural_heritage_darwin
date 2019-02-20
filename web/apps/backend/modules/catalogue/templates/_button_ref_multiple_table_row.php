<?php if (isset($field_id) && isset($row_data)): ?>
  <?php foreach($row_data as $row_data_val_arr): ?>
    <?php if(isset($row_data_val_arr['name']) && isset($row_data_val_arr['level_name'])): ?>
      <tr id="<?php echo $field_id.'_'.$row_data_val_arr['id'];?>" class="catalogue_unit_row">
        <td><?php echo $row_data_val_arr['name'];?></td>
        <td><?php echo $row_data_val_arr['level_name'];?></td>
        <td class="widget_row_delete">
          <?php $link_title=__('Remove this catalogue unit');?>
          <a class="remove_catalogue_unit"
             href="#"
             title="<?php echo $link_title; ?>"
             onclick="$.fn.button_ref_multiple.removeEntry('<?php echo $field_id.'_'.$row_data_val_arr['id'];?>');">
            <?php echo image_tag('remove.png', array('alt'=>$link_title)); ?>
          </a>
        </td>
      </tr>
    <?php endif; ?>
  <?php endforeach; ?>
<?php endif; ?>
