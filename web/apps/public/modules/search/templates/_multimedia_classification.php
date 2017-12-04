<?php if($count):?>
        <h3><?php echo __('Related Files');?>
          <a id="spec_file_expand" title="<?php echo __('Show related files');?>" class="expand_button">
            <?php echo image_tag('blue_expand_up.png', array('alt'=>__('Show related files')));?>
          </a>
        </h3>
        <div id="spec_files_list" class="expand_zone">
        <?php foreach($files as $file):?>
          <?php if( ($type == 'spec' && 
                  in_array($file->getReferencedRelation(), array('specimens','specimen_individuals','specimen_parts')) )
                || $type == $file->getReferencedRelation()):?>
            <?php include_partial('multimedia_element', array('file' => $file)) ; ?>
          <?php endif;?>
        <?php endforeach;?>
        </div>
    <br class="clear" />
<?php endif;?>
