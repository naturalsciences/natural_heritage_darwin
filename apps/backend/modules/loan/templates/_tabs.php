<?php if(! (isset($view) && $view)):?>
<div class="tabs">
  <?php if($loan->isNew()):?>
    <a class="enabled selected" id="tab_0"> &lt; <?php echo __('New loan');?> &gt; </a>
    <a class="disabled" id="tab_1"><?php echo __('Items overview');?></a>
    <a class="disabled" id="tab_2"><?php echo __('Edit item');?></a>

  <?php elseif(!$loan->isNew() && !isset($item) && !isset($items)):?>
    <a class="enabled selected" id="tab_0"> &lt; <?php echo __('Edit loan');?> &gt; </a>
    <?php echo link_to(__('Items overview'), 'loan/overview?id='.$loan->getId(), array('class'=>'enabled', 'id'=> 'tab_1'));?>
    <a class="disabled" id="tab_2"><?php echo __('Edit item');?></a>

  <?php elseif(!$loan->isNew() && isset($items) ):?>
    <?php echo link_to( __('Edit loan' ), 'loan/edit?id='.$loan->getId(), array('class'=>'enabled', 'id'=> 'tab_0'));?>
    <a class="enabled selected" id="tab_1"> &lt; <?php echo __('Items overview');?>  &gt; </a>
    <a class="disabled" id="tab_2"><?php echo __('Edit item');?></a>

  <?php elseif(!$loan->isNew() && isset($item)):?>
    <?php echo link_to(__('Edit loan'), 'loan/edit?id='.$loan->getId(), array('class'=>'enabled', 'id'=> 'tab_0'));?>
    <?php echo link_to(__('Items overview'), 'loan/overview?id='.$loan->getId(), array('class'=>'enabled', 'id'=> 'tab_1'));?>
    <a class="enabled selected" id="tab_2"> &lt; <?php echo __('Edit item');?> &gt; </a>
  <?php endif;?>
</div>


<?php else:?>

<div class="tabs_view">
  <?php if(!$loan->isNew() && !isset($item) && !isset($items)):?>
    <a class="enabled selected" id="tab_0"> &lt; <?php echo __('View loan');?> &gt; </a>
    <?php echo link_to(__('Items overview'), 'loan/overviewView?id='.$loan->getId(), array('class'=>'enabled', 'id'=> 'tab_1'));?>
    <a class="disabled" id="tab_2"><?php echo __('View item');?></a>

  <?php elseif(!$loan->isNew() && isset($items) ):?>
    <?php echo link_to(__('View loan'), 'loan/view?id='.$loan->getId(), array('class'=>'enabled', 'id'=> 'tab_0'));?>
    <a class="enabled selected" id="tab_1"> &lt; <?php echo __('Items overview');?>  &gt; </a>
    <a class="disabled" id="tab_2"><?php echo __('View item');?></a>

  <?php elseif(!$loan->isNew() && isset($item)):?>
    <?php echo link_to(__('View loan'), 'loan/view?id='.$loan->getId(), array('class'=>'enabled', 'id'=> 'tab_0'));?>
    <?php echo link_to(__('Items overview'), 'loan/overviewView?id='.$loan->getId(), array('class'=>'enabled', 'id'=> 'tab_1'));?>
    <a class="enabled selected" id="tab_2"> &lt; <?php echo __('View item');?> &gt; </a>
  <?php endif;?>
</div>

<?php endif;?>
