<?php use_helper('Text');?>  
<table class="catalogue_table">
  <thead>
    <tr>
      <th></th>
      <th><?php echo __('Link');?></th>
      <th><?php echo __('Comment');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($links as $link):?>
  <tr>

    <td>
      <?php echo link_to(image_tag('edit.png'),'extlinks/extLinks?table='.$table.'&cid='.$link->getId().'&id='.$eid,array('class' => 'link_catalogue','title' => __('Edit Url') )) ; ?>  
    </td>

    <td>
      <a href="<?php echo $link->getUrl();?>" target="_pop" class='complete_widget'>
        <?php echo truncate_text($link->getUrl(), 40);?>
      </a>
    </td>
    <td>
      <div title="<?php echo $link->getComment();?>"><?php echo truncate_text($link->getComment(), 50);?></div>
    </td>
    <td class="widget_row_delete">   
      <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=ext_links&id='.$link->getId());?>" title="<?php echo __('Delete Link') ?>"><?php echo image_tag('remove.png'); ?>
      </a>
    </td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>

<br />
<?php echo image_tag('add_green.png');?><a title="<?php echo __('Add Link');?>" class="link_catalogue" href="<?php echo url_for('extlinks/extLinks?table='.$table.'&id='.$eid);?>"><?php echo __('Add');?></a>
