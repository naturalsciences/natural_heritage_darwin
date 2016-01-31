<?php use_helper('Text');?>
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
    <?php if($link->getType() == 'vc') : ?>
       <a href="<?php echo $link->getUrl();?>" target="_pop" class='complete_widget'>
        <?php echo __('Virtual collections link');?>
      </a>
    <?php else : ?>
      <a href="<?php echo $link->getUrl();?>" target="_pop" class='complete_widget'>
        <?php echo __('External Url');?>
      </a>
    <?php endif ; ?>
    </td>
    <td>
      <div title="<?php echo $link->getComment();?>"><?php echo truncate_text($link->getComment(), 50);?></div>
    </td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>
