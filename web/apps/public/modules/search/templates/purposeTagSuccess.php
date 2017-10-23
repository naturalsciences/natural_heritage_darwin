<ul>
  <?php foreach($tags as $tag):?>
    <li class="tag_size_2<?php echo (! isset($tag['precision']))?' unprecise_tag':''?>"><?php echo $tag['tag'];?></li>
  <?php endforeach;?>
</ul>
