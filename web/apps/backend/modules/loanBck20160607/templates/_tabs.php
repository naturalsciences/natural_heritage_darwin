<?php if(! (isset($view) && $view)):?>
<div class="tabs tab_actions">
  <?php if($loan->isNew()):?>
    <a class="enabled selected" id="tab_0"> &lt; <?php echo __('New loan');?> &gt; </a>
    <a class="disabled" id="tab_1"><?php echo __('Items overview');?></a>
    <a class="disabled" id="tab_2"><?php echo __('Edit item');?></a>

  <?php elseif(!$loan->isNew() && !isset($item) && !isset($items)):?>
    <a class="enabled selected with_actions" id="tab_0"> &lt; <?php echo __('Edit loan');?> &gt; </a>
    <?php include_partial('loan/itemactions', array('source' => 'loan', 'action'=>'edit','id'=>$loan->getId())); ?>
    <?php echo link_to(__('Items overview'), 'loan/overview?id='.$loan->getId(), array('class'=>'enabled with_actions', 'id'=> 'tab_1'));?>
    <?php include_partial('loan/itemactions', array('source' => 'loan','action'=>'overview','id'=>$loan->getId())); ?>
    <a class="disabled" id="tab_2"><?php echo __('Edit item');?></a>

  <?php elseif(!$loan->isNew() && isset($items) ):?>
    <?php echo link_to( __('Edit loan' ), 'loan/edit?id='.$loan->getId(), array('class'=>'enabled with_actions', 'id'=> 'tab_0'));?>
    <?php include_partial('loan/itemactions', array('source' => 'loan', 'action'=>'edit','id'=>$loan->getId())); ?>
    <a class="enabled selected with_actions" id="tab_1"> &lt; <?php echo __('Items overview');?>  &gt; </a>
    <?php include_partial('loan/itemactions', array('source' => 'loan','action'=>'overview','id'=>$loan->getId())); ?>
    <a class="disabled" id="tab_2"><?php echo __('Edit item');?></a>

  <?php elseif(!$loan->isNew() && isset($item)):?>
    <?php echo link_to(__('Edit loan'), 'loan/edit?id='.$loan->getId(), array('class'=>'enabled with_actions', 'id'=> 'tab_0'));?>
    <?php include_partial('loan/itemactions', array('source' => 'loan', 'action'=>'edit','id'=>$loan->getId())); ?>
    <?php echo link_to(__('Items overview'), 'loan/overview?id='.$loan->getId(), array('class'=>'enabled with_actions', 'id'=> 'tab_1'));?>
    <?php include_partial('loan/itemactions', array('source' => 'loan','action'=>'overview','id'=>$loan->getId())); ?>
    <a class="enabled selected with_actions" id="tab_2"> &lt; <?php echo __('Edit item');?> &gt; </a>
    <?php include_partial('loan/itemactions', array('source' => 'loanitem','action'=>'edit','id'=>$item->getId())); ?>
  <?php endif;?>
</div>


<?php else:?>

<div class="tabs_view tab_actions">
  <?php if(!$loan->isNew() && !isset($item) && !isset($items)):?>
    <a class="enabled selected with_actions" id="tab_0"> &lt; <?php echo __('View loan');?> &gt; </a>
    <?php include_partial('loan/itemactions', array('source' => 'loan', 'action'=>'view','id'=>$loan->getId())); ?>
    <?php echo link_to(__('Items overview'), 'loan/overviewView?id='.$loan->getId(), array('class'=>'enabled with_actions', 'id'=> 'tab_1'));?>
    <?php include_partial('loan/itemactions', array('source' => 'loan','action'=>'overviewView','id'=>$loan->getId())); ?>
    <a class="disabled" id="tab_2"><?php echo __('View item');?></a>

  <?php elseif(!$loan->isNew() && isset($items) ):?>
    <?php echo link_to(__('View loan'), 'loan/view?id='.$loan->getId(), array('class'=>'enabled with_actions', 'id'=> 'tab_0'));?>
    <?php include_partial('loan/itemactions', array('source' => 'loan', 'action'=>'view','id'=>$loan->getId())); ?>
    <a class="enabled selected with_actions" id="tab_1"> &lt; <?php echo __('Items overview');?>  &gt; </a>
    <?php include_partial('loan/itemactions', array('source' => 'loan','action'=>'overviewView','id'=>$loan->getId())); ?>
    <a class="disabled" id="tab_2"><?php echo __('View item');?></a>

  <?php elseif(!$loan->isNew() && isset($item)):?>
    <?php echo link_to(__('View loan'), 'loan/view?id='.$loan->getId(), array('class'=>'enabled with_actions', 'id'=> 'tab_0'));?>
    <?php include_partial('loan/itemactions', array('source' => 'loan', 'action'=>'view','id'=>$loan->getId())); ?>
    <?php echo link_to(__('Items overview'), 'loan/overviewView?id='.$loan->getId(), array('class'=>'enabled with_actions', 'id'=> 'tab_1'));?>
    <?php include_partial('loan/itemactions', array('source' => 'loan','action'=>'overviewView','id'=>$loan->getId())); ?>
    <a class="enabled selected with_actions" id="tab_2"> &lt; <?php echo __('View item');?> &gt; </a>
    <?php include_partial('loan/itemactions', array('source' => 'loanitem','action'=>'view','id'=>$item->getId())); ?>
  <?php endif;?>
</div>

<?php endif;?>

<script  type="text/javascript">
$(document).ready(function()
{

   $('.tab_actions').delegate('a.with_actions', 'mouseover', function(event) {
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
