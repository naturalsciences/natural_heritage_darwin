<?php use_helper('Text');?>
<table class="catalogue_table">
  <thead>
    <tr>
      <th><?php echo __('Notion');?></th>
      <th><?php echo __('Comment');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($comments as $comment):?>
  <tr>
    <td>
      <a class="link_catalogue" title="<?php echo __('Edit Comment');?>"
	  href="<?php echo url_for('comment/comment?table='.$table.'&cid='.$comment->getId().'&id='.$eid); ?>">
	    <?php echo $comment->getNotionText();?>
      </a>
    </td>
    <td>
      <?php echo auto_link_text( nl2br( $comment->getComment() ));?>
    </td>
    <td class="widget_row_delete">
      <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=comments&id='.$comment->getId());?>" title="<?php echo __('Delete Comment') ?>"><?php echo image_tag('remove.png'); ?>
      </a>
    </td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>

<br />
<?php echo image_tag('add_green.png');?><a title="<?php echo __('Add Comment');?>" class="link_catalogue" href="<?php echo url_for('comment/comment?table='.$table.'&id='.$eid);?>"><?php echo __('Add');?></a>
