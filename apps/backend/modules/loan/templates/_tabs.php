<div class="tabs<?php if(isset($view) && $view) echo '_view' ; ?>">
  <?php if($loan->isNew()):?>
    <a class="enabled selected" id="tab_0"> &lt; <?php echo __('New Loan');?> &gt; </a>
    <a class="disabled" id="tab_1"><?php echo __('Items overview');?></a>
    <a class="disabled" id="tab_2"><?php echo __('Item');?></a>

  <?php elseif(!$loan->isNew() && !isset($item) && !isset($items)):?>
    <a class="enabled selected" id="tab_0"> &lt; <?php echo __('Edit Loan');?> &gt; </a>
    <?php echo link_to(__('Items overview'), 'loan/overview?id='.$loan->getId(), array('class'=>'enabled', 'id'=> 'tab_1'));?>
    <a class="disabled" id="tab_2"><?php echo __('Item');?></a>

  <?php elseif(!$loan->isNew() && isset($items) ):?>
    <?php echo link_to(__('Edit Loan'), 'loan/edit?id='.$loan->getId(), array('class'=>'enabled', 'id'=> 'tab_0'));?>
    <a class="enabled selected" id="tab_1"> &lt; <?php echo __('Items overview');?>  &gt; </a>
    <a class="disabled" id="tab_2"><?php echo __('Item');?></a>

  <?php elseif(!$loan->isNew() && isset($item) /*&& $item->isNew() */):?>
    <?php echo link_to(__('Edit Loan'), 'loan/edit?id='.$loan->getId(), array('class'=>'enabled', 'id'=> 'tab_0'));?>
    <a class="enabled" id="tab_1"><?php echo __('Items overview');?></a>
    <a class="enabled selected" id="tab_2"> &lt; <?php echo __('Item');?> &gt; </a>
  <?php endif;?>
</div>