<?php echo $spec->getIgNum() ; ?>
<?php if ($spec->getIgNum()) : ?>
  <?php echo link_to(' ', 'igs/view?id='.$spec->getIgRef(), array('class' => 'but_more','title'=>__('View I.G.'),'target'=>'_blank' )) ; ?>
<?php endif; ?>
