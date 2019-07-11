<div class="hidden encod_tip">
  <ul>
    <li>
      <?php if($action=='edit' || $action=='overview'):?>
        <?php echo image_tag('blue_eyel.png', array("title" => __("View")));?>
        <?php if($action=='edit'):?>
          <?php echo link_to(__('View'), $source.'/view?id='.$id); ?>
        <?php else:?>
          <?php echo link_to(__('View'), $source.'/'.$action.'View?id='.$id); ?>
        <?php endif;?>
      <?php else:?>
        <?php echo image_tag('edit.png', array("title" => __("Edit")));?>
        <?php if($action=='view'):?>
          <?php echo link_to(__('Edit'), $source.'/edit?id='.$id); ?>
        <?php else:?>
          <?php echo link_to(__('Edit'), $source.'/overview?id='.$id); ?>
        <?php endif;?>
      <?php endif;?>
    </li>
  </ul>
</div>
