<ul>
  <?php foreach($tags as $tag):?>
    <li class="tag_size_<?php echo $tag['size'];?><?php echo (! isset($tag['precision']) )?' unprecise_tag':''?>"><?php echo $tag['tag'];?></li>
  <?php endforeach;?>
</ul>
