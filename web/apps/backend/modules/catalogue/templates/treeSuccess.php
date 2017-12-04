<h3><?php echo __('Details :');?></h3>
<ul>
<?php foreach($items as $i=>$item):?>
  <li style="margin-left: <?php echo $i;?>em">
    <?php if($i != 0):?>
      <?php echo image_tag('tree_spacer.gif');?>
    <?php endif;?>
    <?php echo $item->getNameWithFormat(ESC_RAW);?>
      <?php if($table != 'collections'):?>
        (<?php echo $item->getLevel()->getLevelName();?>)
      <?php endif;?>
  </li>
<?php endforeach;?>
</ul>
