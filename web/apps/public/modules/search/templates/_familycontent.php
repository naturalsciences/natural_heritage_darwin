<?php if (isset($familycontent) &&
          isset($family) &&
          count($familycontent) > 0): ?>
  <section id="family-<?php echo $family->getName();?>">
    <h1><?php echo __('List of specimens for family familyName', array('familyName'=>$family->getName()));?></h1>
    <?php $previousLine = ''; ?>
    <?php foreach ($familycontent as $line): ?>
      <?php if ($previousLine !== $line['collection_name']): ?>
        <?php if ($previousLine !== '' && $line['collection_name'] !== $previousLine): ?>
          </section>
        <?php endif; ?>
        <section id="collection-<?php echo $line['collection_name'];?>">
          <h2><?php echo __('Collection collectionName', array('collectionName'=>$line['collection_name'])); ?></h2>
          <p><a href="http://darwin.naturalsciences.be/search/view/id/<?php echo $line['id'] ;?>" title="<?php echo $line['taxon_name'] ;?>" target="_blank"><?php echo $line['taxon_name'] ;?></a></p>
      <?php else: ?>
          <p><a href="http://darwin.naturalsciences.be/search/view/id/<?php echo $line['id'] ;?>" title="<?php echo $line['taxon_name'] ;?>" target="_blank"><?php echo $line['taxon_name'] ;?></a></p>
      <?php endif; ?>
      <?php $previousLine = $line['collection_name']; ?>
    <?php endforeach; ?>
  </section>
<?php else: ?>
<p>Nothing to show</p>
<?php endif; ?>