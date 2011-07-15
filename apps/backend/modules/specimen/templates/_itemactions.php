<div class="hidden encod_tip">
  <ul>
    <li>
      <?php if($sf_user->isPinned($id, $source)):?>
        <?php echo image_tag('blue_pin_on.png', array('class'=>'pin_but pin_on'));?>
        <?php echo image_tag('blue_pin_off.png', array('class'=>'pin_but pin_off hidden'));?>
      <?php else:?>
        <?php echo image_tag('blue_pin_on.png', array('class'=>'pin_but pin_on hidden'));?>
        <?php echo image_tag('blue_pin_off.png', array('class'=>'pin_but pin_off'));?>
      <?php endif;?>
      <?php echo link_to(__('Pin'), 'savesearch/pin?source='.$source.'&id='.$id, array('class'=>'pin_link'));?>
    </li>
    <li>
      <?php echo image_tag('blue_eyel.png', array("title" => __("View")));?>
      <?php if($source != 'specimen') $source .='s';?>
      <?php echo link_to(__('View only'), $source.'/view?id='.$id); ?>
    </li>
  </ul>
</div>