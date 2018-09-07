<?php 
$has_one_visible = false;
$changed_col=false;
if(!isset($encod)) $encod = true;
if(!isset($columns)) $columns = 1;
?>
<ul class="board_col<?php if($encod) echo ' encod_screen';?><?php if($columns==1) echo ' one_col';?>">  
  <?php foreach($widgets as $id => $widget):?>
    <?php if(!$widget->getVisible()) continue;?>

    <?php if($columns != 1 && $changed_col === false && $widget->getColNum() == 2):?>
	<?php $changed_col = true;?>
	</ul>
	<div class="board_spacer">&nbsp;</div>
	<ul class="board_col<?php if($encod) echo ' encod_screen';?>">
    <?php endif;?>
    <?php include_partial('widgets/wlayout', array(
      'widget' => $widget->getGroupName(),
      'title' => $widget->getTitlePerso(),
      'is_opened' => $widget->getOpened(),
      'is_mandatory' => $widget->getMandatory(),
      'category' => $category,
      'options' => $options->getRawValue(),
      )); ?>
    <?php $has_one_visible=true;?>
  <?php endforeach;?>

  <?php if($columns != 1 && $changed_col === false):?>
      </ul>
      <div class="board_spacer">&nbsp;</div>
      <ul class="board_col<?php if($encod) echo ' encod_screen';?>">
  <?php endif;?>

</ul>
<div class="no_more_wigets<?php if($has_one_visible) echo ' hidden';?>"><?php echo __("There are no widgets here. Pick one from the widgets collection.");?></div>
