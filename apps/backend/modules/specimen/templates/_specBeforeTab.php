<?php use_stylesheet('encod.css') ?>
<script  type="text/javascript">
$(document).ready(function()
{

  $('.pin_link').click(function(event)
  {
    event.preventDefault();
    $(this).parent().find('.pin_but').not('.hidden').trigger('click');
  });
  $('.pin_but').click(function(){
    if($(this).hasClass('pin_on'))
    {
      $(this).parent().find('.pin_off').removeClass('hidden'); 
      $(this).addClass('hidden') ;
      pin_status = 0;
    }
    else
    {
      $(this).parent().find('.pin_on').removeClass('hidden');
      $(this).addClass('hidden') ;
      pin_status = 1;
    }
    $.get( $(this).parent().find('.pin_link').attr('href') + '/status/' + pin_status,function (html){});
  });

   $('.tabs').delegate('a.with_actions', 'mouseover', function(event) {
      event.preventDefault();
      var self = $(this),
         container = $(event.liveFired),
 
      // Determine whether this is a top-level menu
      //isTopMenu = self.parents(qtip).length < 1;
 
      /*
       * Top-level menus will be placed below the menu item, all others
       * will be placed to the right of each other, top aligned.
       */
      position = { my: 'top left', at: 'bottom left' } ;
      // Create the tooltip
      self.qtip({
         overwrite: false, // Make sure we only render one tooltip
         content: self.next('.encod_tip'), // Use the submenu as the qTip content
         position: position, /*$.extend(true, position, {
            // Append the nav tooltips to the #navigation element (see show.solo below)
          //  container: container,
 
            // We'll make sure the menus stay visible by shifting/flipping them back into the viewport
            //viewport: $(window), adjust: { method: 'shift flip' }
         })*/
         show: {
            event: event.type, // Make sure to sue the same event as above
            ready: true // Make sure it shows on first mouseover
 
            /*
             * If it's a top level menu, make sure only one is shown at a time!
             * We'll pass the container element through too so it doesn't hide
             * tooltips unrelated to the menu itself
             */
           //solo: container
         },
         hide: {
            delat: 400,
            fixed: true // Make sure we can interact with the qTip by setting it as fixed
         },
         style: {
            classes: 'ui-tooltip-nav encod_tooltip', // Basic styles
            tip: false // We don't want a tip... it's a menu duh!
         }
      });
   });
});

</script>
<?php $specimen_id = ($specimen->isNew())?'':$specimen->getId();?>
<?php $specimen_name = ($specimen->isNew())?'': sprintf(__('Specimen %d'),$specimen->getId());?>
<?php $individual_id = '';?>
<?php $individual_name = '';?>
<?php $part_tab_class = 'disabled';?>
<?php $ind_num = '<span class="tab_item_count"> #'.$specimen->getNbrIndiv().'</span>';?>
<?php $part_num = '<span class="tab_item_count"> #'.$specimen->getNbrPart().'</span>';?>
<?php if(isset($individual)):?>
  <?php $part_num = '<span class="tab_item_count"> #'.$individual->getNbrPart().'</span>';?>
  <?php $individual_id = ($individual->isNew())?'':$individual->getId();?>
  <?php $individual_name = ($individual->isNew())?__('New Individual'):__('Individual ').$individual_id;?>
  <?php $part_tab_class = ($individual_id == '')?'disabled':'enabled';?>
<?php endif;?>

<div class="encoding">
	<div class="page">
		<div class="tabs<?php if(isset($view) && $view) echo '_view' ; ?>">
			  <?php if($specimen->isNew()):?>

          <a class="enabled selected" id="tab_0"> &lt; <?php echo __('New Specimen');?> &gt; </a>
          <a class="disabled" id="tab_1"><?php echo __('Individuals overview').$ind_num;?></a>
          <a class="disabled" id="tab_2"><?php echo __('New Individual');?></a>
          <a class="disabled" id="tab_3"><?php echo __('Parts overview').$part_num;?></a>
          <a class="disabled" id="tab_4"><?php echo __('New Part');?></a>

			  <?php elseif($mode == 'specimen_edit'):?>

          <a class="enabled selected with_actions" id="tab_0"> &lt; <?php echo $specimen_name;?> &gt; </a>
          <?php include_partial('specimen/itemactions', array('source' => 'specimen','id'=>$specimen_id)); ?>
          <?php echo link_to(__('Individuals overview').$ind_num, 'individuals/overview?spec_id='.$specimen_id, array('class'=>'enabled', 'id' => 'tab_1'));?>
          <?php echo link_to(__('New Individual'), 'individuals/edit?spec_id='.$specimen_id, array('class'=>'enabled', 'id' => 'tab_2'));?>
          <a class="disabled" id="tab_3"><?php echo __('Parts overview').$part_num;?></a>
          <a class="disabled" id="tab_4"><?php echo __('New Part');?></a>

			  <?php elseif($mode == 'individuals_overview' ):?>
          <?php if($view) : ?>

            <a class="enabled" id="tab_0" href="<?php echo url_for('specimen/view?id='.$specimen_id);?>"><?php echo $specimen_name;?></a>
            <a class="enabled selected" id="tab_1"> &lt; <?php echo __('Individuals overview').$ind_num;?> &gt; </a>
            <a class="disabled" id="tab_3"><?php echo __('Parts overview');?></a>

          <?php else : ?>

            <a class="enabled with_actions" id="tab_0" href="<?php echo url_for('specimen/edit?id='.$specimen_id);?>"><?php echo $specimen_name;?></a>
            <?php include_partial('specimen/itemactions', array('source' => 'specimen','id'=>$specimen_id)); ?>
            <a class="enabled selected" id="tab_1"> &lt; <?php echo __('Individuals overview').$ind_num;?> &gt; </a>			
            <?php echo link_to(__('New Individual'), 'individuals/edit?spec_id='.$specimen_id, array('class'=>'enabled', 'id' => 'tab_2'));?>
            <a class="disabled" id="tab_3"><?php echo __('Parts overview').$part_num;?></a>
            <a class="disabled" id="tab_4"><?php echo __('New Part');?></a>

          <?php endif ; ?>

			  <?php elseif($mode == 'individual_edit'):?>

          <a class="enabled with_actions" id="tab_0" href="<?php echo url_for('specimen/edit?id='.$specimen_id);?>"><?php echo $specimen_name;?></a>
          <?php include_partial('specimen/itemactions', array('source' => 'specimen','id'=>$specimen_id)); ?>
          <?php echo link_to(__('Individuals overview').$ind_num, 'individuals/overview?spec_id='.$specimen_id, array('class'=>'enabled', 'id' => 'tab_1'));?>
          <a class="enabled selected with_actions" id="tab_2"> &lt; <?php echo $individual_name;?> &gt; </a>
          <?php include_partial('specimen/itemactions', array('source' => 'individual','id'=>$individual_id)); ?>
          <?php echo link_to(__('Parts overview').$part_num, ($individual_id=='')?'individuals/edit?spec_id='.$specimen_id:'parts/overview?id='.$individual_id, array('id'=>'tab_3', 'class'=>$part_tab_class)); ?>
          <?php echo link_to(__('New Part'), ($individual_id=='')?'individuals/edit?spec_id='.$specimen_id:'parts/edit?indid='.$individual_id, array('id'=>'tab_4', 'class'=>$part_tab_class)); ?>

			  <?php elseif($mode == 'parts_overview'):?>

          <?php if($view) : ?>

            <?php echo link_to($specimen_name, 'specimen/view?id='.$specimen_id, array('class'=>'enabled', 'id' => 'tab_0'));?>        
            <?php echo link_to(__('Individuals overview').$ind_num, 'individuals/overview?spec_id='.$specimen_id.'&view=true', array('class'=>'enabled', 'id' => 'tab_1'));?>
            <?php echo link_to($individual_name, 'individuals/view?id='.$individual_id, array('class'=>'enabled', 'id' => 'tab_2'));?>
            <a class="enabled selected" id="tab_3"> &lt; <?php echo __('Parts overview').$part_num;?> &gt; </a>

          <?php else : ?>

            <a class="enabled with_actions" id="tab_0" href="<?php echo url_for('specimen/edit?id='.$specimen_id);?>"><?php echo $specimen_name;?></a>
            <?php include_partial('specimen/itemactions', array('source' => 'specimen','id'=>$specimen_id)); ?>            
            <?php echo link_to(__('Individuals overview').$ind_num, 'individuals/overview?spec_id='.$specimen_id, array('class'=>'enabled', 'id' => 'tab_1'));?>
            <?php echo link_to($individual_name, 'individuals/edit?id='.$individual_id, array('class'=>'enabled with_actions', 'id' => 'tab_2'));?>
            <?php include_partial('specimen/itemactions', array('source' => 'individual','id'=>$individual_id)); ?>
            <a class="enabled selected" id="tab_3"> &lt; <?php echo __('Parts overview').$part_num;?> &gt; </a>
            <?php echo link_to(__('New Part'), 'parts/edit?indid='.$individual_id, array('class'=>'enabled', 'id' => 'tab_4'));?>

          <?php endif ; ?>

			  <?php else:?>

          <a class="enabled with_actions" id="tab_0" href="<?php echo url_for('specimen/edit?id='.$specimen_id);?>"><?php echo $specimen_name;?></a>
          <?php include_partial('specimen/itemactions', array('source' => 'specimen','id'=>$specimen_id)); ?>
          <?php echo link_to(__('Individuals overview').$ind_num, 'individuals/overview?spec_id='.$specimen_id, array('class'=>'enabled', 'id' => 'tab_1'));?>
          <?php echo link_to($individual_name, 'individuals/edit?id='.$individual_id, array('class'=>'enabled with_actions', 'id' => 'tab_2'));?>
          <?php include_partial('specimen/itemactions', array('source' => 'individual','id'=>$individual_id)); ?>
          <?php echo link_to(__('Parts overview').$part_num, 'parts/overview?id='.$individual_id, array('class'=>'enabled', 'id' => 'tab_3'));?>
          <?php if ($sf_user->isAtLeast(Users::ENCODER)): ?>

            <a class="enabled selected<?php if(!$part->isNew()):?> with_actions<?php endif;?>" id="tab_4">				
              &lt; <?php if($part->isNew()):?>
              <?php echo __('New Part'); ?>
              <?php else:?>
              <?php echo __('Edit Part'); ?>
              <?php endif;?> &gt; 
            </a>
            <?php if(!$part->isNew()):?>

              <?php include_partial('specimen/itemactions', array('source' => 'part','id'=> $part->getId())); ?>

            <?php endif;?>

          <?php endif ; ?>

			  <?php endif;?>
		  </div>
 		<div class="<?php if(isset($view) && $view) echo 'panel_view' ; else echo 'panel edition ' ?> encod_screen" id="intro">
