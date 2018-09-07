<table class="catalogue_table_view">
  <thead>
    <tr>
      <th><?php echo __('Url');?></th>
      <th><?php echo __('Comment');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($links as $link):?>
  <tr>
    <td>
      <?php echo $link->getUrl();?>
      <a href="<?php echo $link->getUrl();?>" target="_pop" class='complete_widget'>
      <?php echo image_tag('next.png',array('title'=>__('Go to this link'))) ; ?>
      </a>        
    </td>
    <td>
      <?php echo $link->getComment();?>
    </td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>
