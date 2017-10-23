<h3><?php echo __('Details :');?></h3>
<ul>
<?php foreach($items as $i=>$item):?>
  <li style="margin-left: <?php echo $i;?>em">
    <?php if($i != 0):?>
      <?php echo image_tag('tree_spacer.gif');?>
    <?php endif;?>
    <?php echo html_entity_decode($item->getNameWithFormat());?>
  </li>
<?php endforeach;?>
</ul>
