<?php slot('title', __('Browse Collections'));  ?>        
<div class="page">
    <h1><?php echo __('Collection List');?></h1>
    <?php include_partial('collectionTree', array('institutions' => $institutions,
                                                  'is_choose' => false)) ?>
</div>
