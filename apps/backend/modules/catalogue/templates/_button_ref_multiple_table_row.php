<?php if (isset($field_id) && isset($row_id) && is_array($row_id)): ?>
  <?php foreach($row_id as $row_id_val): ?>
    <tr id="<?php echo $field_id.'_'.$row_id_val['id'];?>" class="catalogue_unit_row">
      <td><?php echo 'name';//$name;?></td>
      <td><?php echo 'level';//$level;?></td>
      <td class="widget_row_delete">
        <?php $link_title=__('Remove this catalogue unit');?>
        <a class="remove_catalogue_unit"
           href="#"
           title="<?php echo $link_title; ?>"
           onclick="$.fn.button_ref_multiple.removeEntry('<?php echo $field_id.'_'.$row_id_val['id'];?>');">
          <?php echo image_tag('remove.png', array('alt'=>$link_title)); ?>
        </a>
      </td>
    </tr>
  <?php endforeach; ?>
<?php endif; ?>