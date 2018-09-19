<!--JMHerpers 2018 04 20-->
    <a class="maint_butt<?php if($lineObj->isNew()) echo 'disabled';?>" href="#">
      <?php echo image_tag( ($lineObj->isNew() ? 'grey' : 'individual' ).'_expand.png');?> <?php echo __('Maintenances');?>
    </a>
