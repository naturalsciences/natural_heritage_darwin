<?php use_helper('Text');?>
<?php foreach($Ecology as $eco_item):?>
  <fieldset class="opened"><legend><b><?php echo __('Notion');?></b> : <?php echo __($eco_item->getNotionText());?></legend>
    <?php echo auto_link_text( nl2br($eco_item->getComment())) ;?>
  </fieldset>
<?php endforeach ; ?>    
