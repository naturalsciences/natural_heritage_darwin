<dl>
    <dt><?php echo __('Collection :');?></dt>
    <dd><?php echo $item->Individual->SpecimensFlat->getCollectionName();?></dd>
    <dt><?php echo __('Taxonomy :');?></dt>
    <dd><?php echo $item->Individual->SpecimensFlat->getTaxonName();?></dd>
    <dt><?php echo __('Sampling Location :');?></dt>
    <dd><?php echo $item->Individual->SpecimensFlat->getGtu(ESC_RAW);?></dd>
    <dt><?php echo __('Type :');?></dt>
    <dd><?php echo $item->Individual->getTypeGroup();?></dd>
    <dt><?php echo __('Sex :');?></dt>
    <dd><?php echo $item->Individual->getSex();?></dd>
    <dt><?php echo __('State :');?></dt>
    <dd><?php echo $item->Individual->getState();?></dd>
    <?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
    <dt><?php echo __('Building :');?></dt>
    <dd><?php echo $item->getBuilding();?></dd>
    <dt><?php echo __('Floor :');?></dt>
    <dd><?php echo $item->getFloor();?></dd>
    <dt><?php echo __('Room :');?></dt>
    <dd><?php echo $item->getRoom();?></dd>
    <dt><?php echo __('Row :');?></dt>
    <dd><?php echo $item->getRow();?></dd>
    <dt><?php echo __('Shelf :');?></dt>
    <dd><?php echo $item->getShelf();?></dd>
    <?php endif;?>
</dl>