<table class="catalogue_table<?php echo($level == Users::REGISTERED_USER?'_view':'') ;?>">
  <thead>
    <tr>
      <th><?php echo __('Type');?></th>
      <th><?php echo __('Value');?></th>
      <th><?php echo __('Tags');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($comms as $comm):?>
  <tr>
    <td>
      <?php if($level>Users::REGISTERED_USER) : ?>      
      <a class="link_catalogue" title="<?php echo __('Edit Communication Means');?>"  href="<?php echo url_for('people/comm?ref_id='.$eid.'&id='.$comm->getId());?>">
      <?php if($comm->getCommType()=="phone/fax"):?>
	<?php echo __('Phone');?>
      <?php else:?>
	<?php echo __('e-Mail');?>
      <?php endif;?>
      </a>
      <?php else : ?>
        <?php if($comm->getCommType()=="phone/fax"):?>
	        <?php echo __('Phone');?>
        <?php else:?>
	        <?php echo __('e-Mail');?>      
	      <?php endif ; ?>
      <?php endif ; ?>      
    </td>
    <td>
      <?php echo $comm->getEntry();?>
    </td>
    <td>
      <?php foreach($comm->getTagsAsArray() as $item):?>
	<span class="tag"><?php echo $item;?><?php echo image_tag('tags.gif');?></span>
      <?php endforeach;?>
    </td>

    <td class="widget_row_delete">
      <?php if($level>Users::REGISTERED_USER) : ?>      
      <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=people_comm&id='.$comm->getId());?>" title="<?php echo __('Are you sure ?') ?>"><?php echo image_tag('remove.png'); ?>
      </a>
      <?php endif ; ?>
    </td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>

<br />
<?php if($level>Users::REGISTERED_USER) : ?>  
<?php echo image_tag('add_green.png');?>
<a title="<?php echo __('Add Communication Means');?>" class="link_catalogue" href="<?php echo url_for('people/comm?ref_id='.$eid);?>"><?php echo __('Add');?></a>
<?php endif ; ?>
