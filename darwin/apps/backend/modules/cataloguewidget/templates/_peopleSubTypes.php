<?php use_helper('Text');?>
<table class="catalogue_table">
  <thead>
    <tr>
      <th><?php echo __('Sub type');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($sub_types as $sub_type):?>
  <tr>
    <td>
      <a class="link_catalogue" title="<?php echo __('Edit Sub-type');?>"
	  href="<?php echo url_for('peopleSubTypes/peopleSubTypes?cid='.$sub_type->getId().'&id='.$eid); ?>">
	    <?php print($sub_type->getSubType());?>
      </a>
    </td>
    <td class="widget_row_delete">
      <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=people_sub_types&id='.$sub_type->getId());?>" title="<?php echo __('Delete Category') ?>"><?php echo image_tag('remove.png'); ?>
      </a>
    </td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>

<br />
<?php echo image_tag('add_green.png');?><a title="<?php echo __('Add people category');?>" class="link_catalogue" href="<?php echo url_for('peopleSubTypes/peopleSubTypes?id='.$eid);?>"><?php echo __('Add');?></a>
