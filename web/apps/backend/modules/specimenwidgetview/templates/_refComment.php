<?php use_helper('Text');?>
<?php foreach($Comments as $comment):?>
  <fieldset class="opened view_mode"><legend class="view_mode"><b><?php echo __('Notion');?></b> : <?php echo __($comment->getNotionText());?></legend>
    <?php echo auto_link_text( nl2br($comment->getComment())) ;?>
  </fieldset>
<?php endforeach ; ?>    
