<div class="tabs<?php if(isset($view) && $view) echo '_view' ; ?>">
  <?php if($loan->isNew()):?>
    <a class="enabled selected" id="tab_0"> &lt; <?php echo __('New Loan');?> &gt; </a>
    <a class="disabled" id="tab_1"><?php echo __('Items overview');?></a>
    <a class="disabled" id="tab_2"><?php echo __('Item');?></a>
  <?php elseif(!$loan->isNew() && !isset($item) ):?>
    <a class="enabled selected" id="tab_0"> &lt; <?php echo __('Edit Loan');?> &gt; </a>
    <a class="enabled" id="tab_1"><?php echo __('Items overview');?></a>
    <a class="disabled" id="tab_2"><?php echo __('Item');?></a>
  <?php elseif(!$loan->isNew() && isset($item) /*&& $item->isNew() */):?>
    <a class="enabled" id="tab_0"><?php echo __('Edit Loan');?></a>
    <a class="enabled" id="tab_1"><?php echo __('Items overview');?></a>
    <a class="enabled selected" id="tab_2"> &lt; <?php echo __('Item');?> &gt; </a>
  <?php endif;?>
</div>