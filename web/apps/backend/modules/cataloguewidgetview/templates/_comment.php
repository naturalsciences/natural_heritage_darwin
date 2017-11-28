<?php use_helper('Text');?>
<table class="catalogue_table_view">
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
      <?php echo $comment->getNotionText();?>
    </td>
    <td>
      <?php echo auto_link_text( nl2br( $comment->getComment() ));?>
    </td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>
