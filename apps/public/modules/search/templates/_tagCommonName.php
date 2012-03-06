<?php use_helper('Darwin');?>
<td class="col_taxon_common_name">
  <?php if(isset($common_names['taxonomy'][$spec->getTaxonRef()])) : ?>
    <ul class="common_name_tags">
      <?php foreach($common_names['taxonomy'][$spec->getTaxonRef()]['community'] as $lang => $tag) : ?>
        <li style="border-left:6px solid #<?php echo word2color($lang);?>;" title="<?php echo $lang;?>"><?php echo $tag;?></li>
      <?php endforeach;?>
    </ul>
  <?php endif ; ?>
</td>
<td class="col_chrono_common_name">
  <?php if(isset($common_names['chronostratigraphy'][$spec->getChronoRef()])) : ?>
    <ul class="common_name_tags">
      <?php foreach($common_names['chronostratigraphy'][$spec->getChronoRef()]['community'] as $lang => $tag) : ?>
        <li style="border-left:6px solid #<?php echo word2color($lang);?>;" title="<?php echo $lang;?>"><?php echo $tag;?></li>
      <?php endforeach;?>
    </ul>
  <?php endif ; ?>
</td>
<td class="col_litho_common_name"> 
  <?php if(isset($common_names['lithostratigraphy'][$spec->getLithoRef()])) : ?>
    <ul class="common_name_tags">
      <?php foreach($common_names['lithostratigraphy'][$spec->getChronoRef()]['community'] as $lang => $tag) : ?>
        <li style="border-left:6px solid #<?php echo word2color($lang);?>;" title="<?php echo $lang;?>"><?php echo $tag;?></li>
      <?php endforeach;?>
    </ul>
  <?php endif ; ?>
</td> 
<td class="col_lithologic_common_name">
  <?php if(isset($common_names['lithology'][$spec->getLithologyRef()])) : ?>
    <ul class="common_name_tags">
      <?php foreach($common_names['lithology'][$spec->getChronoRef()]['community'] as $lang => $tag) : ?>
        <li style="border-left:6px solid #<?php echo word2color($lang);?>;" title="<?php echo $lang;?>"><?php echo $tag;?></li>
      <?php endforeach;?>
    </ul>
  <?php endif ; ?>
</td>
<td class="col_mineral_common_name"> 
  <?php if(isset($common_names['mineralogy'][$spec->getMineralRef()])) : ?>
    <ul class="common_name_tags">
      <?php foreach($common_names['mineralogy'][$spec->getChronoRef()]['community'] as $lang => $tag) : ?>
        <li style="border-left:6px solid #<?php echo word2color($lang);?>;" title="<?php echo $lang;?>"><?php echo $tag;?></li>
      <?php endforeach;?>
    </ul>
  <?php endif ; ?>
</td>
