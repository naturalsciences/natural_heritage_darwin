<?php if (isset($field_id) && isset($row_id) && isset($name) && isset($level)): ?>
<tr id="<?php echo $field_id.'_'.$row_id;?>" class="catalogue_unit_row">
  <td><?php echo $name;?></td>
  <td><?php echo $level;?></td>
  <td class="widget_row_delete">
    <?php $link_title=__('Remove this catalogue unit');?>
    <a class="remove_catalogue_unit" href="#" title="<?php echo $link_title; ?>"><?php echo image_tag('remove.png', array('alt'=>$link_title)); ?></a>
  </td>
</tr>
<?php endif; ?>