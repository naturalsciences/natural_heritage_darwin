<?php use_helper('Text','Date');?>

<ul class="board_news">
<?php foreach($arrFeeds as $item):?>
  <li><?php echo auto_link_text($item['title'],'all',array('target'=>'_blank'));?>
    <div class="news_info">
      <span class="date"><?php echo format_datetime($item['date'],'f');?></span> 
      :: <span class="link"><a href="<?php echo $item['link'];?>" target="_blank"><?php echo __('More');?></a></span>
    </div>
  </li>
<?php endforeach;?>
</ul>
