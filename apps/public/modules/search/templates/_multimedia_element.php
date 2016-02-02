<div class="files">
    <?php $alt=($file->getDescription()!='')?$file->getTitle().' / '.$file->getDescription():$file->getTitle();?>
    <?php  if($file->hasPreview()):?>
      <?php echo link_to(image_tag(url_for('multimedia/preview?id='.$file->getId()), array('alt'=> __('Download'), 'width'=>100, 'raw_name'=>true)),
        'multimedia/downloadFile?id='.$file->getId(), array('title' => $alt )) ; ?>
    <?php else:?>
      <?php echo link_to(image_tag('criteria.png', array('alt'=> __('Download')) ),'multimedia/downloadFile?id='.$file->getId(), array('title' => $alt)) ; ?>
    <?php endif;?>
    <div class="file_title" title="<?php echo $alt;?>"><?php echo $file->getTitle();?></div>
</div>
