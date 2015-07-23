<?php if (isset($field_id) && isset($row_num) && isset($name) && isset($level)): ?>
<tr id="<?php echo $field_id.'_'.$row_num;?>">
  <td><?php echo $name;?></td>
  <td><?php echo $level;?></td>
  <td class="widget_row_delete">
    <a><?php echo image_tag('remove.png', array('alt'=>__('Remove this catalogue unit')); ?></a>
  </td>
</tr>
<?php endif; ?>