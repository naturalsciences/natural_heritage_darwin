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
<?php $specimen_name = ($specimen->isNew())?'': sprintf(__('Specimen s%d'),$specimen->getId());?>

<div class="encoding">
  <div class="page">
    <div class="tabs<?php if(isset($view) && $view) echo '_view' ; ?>">
      <?php if($specimen->isNew()):?>
        <a class="enabled selected" id="tab_0">
          &lt; <?php echo __('New Specimen');?> &gt;
        </a>
      <?php elseif($mode == 'specimen_edit'):?>
          <a class="enabled selected with_actions" id="tab_0"> &lt; <?php echo $specimen_name;?> &gt; </a>
      <?php endif;?>
    </div>
<div class="<?php if(isset($view) && $view) echo 'panel_view' ; else echo 'panel edition ' ?> encod_screen" id="intro">