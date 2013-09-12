<div class="menu_top">
    <ul id="navigation">
        <li class="house"><?php echo link_to(image_tag('home.png', 'alt=Home'),'board/index');?></li>
        <li>
            <a href="#" class="subtitle"><?php echo __('My Preferences');?></a>
            <ul class="submenu">
              <li><?php echo link_to(__('My Profile'),'user/profile');?></li>
              <li><?php echo link_to(__('My Widgets'),'user/widget');?></li>
              <li><?php echo link_to(__('My Preferences'),'user/preferences');?></li>
              <li><?php echo link_to(__('Saved Specimens list'),'savesearch/index?specimen=1');?></li>
              <li><?php echo link_to(__('Saved search'),'savesearch/index');?></li>
            </ul>
        </li>
        <li>
            <a href="#" class="subtitle"><?php echo __('Searches');?></a>
            <ul class="submenu">
                <li>
                    <a href="#" class="subtitle"><?php echo __('Catalogues');?> »</a>
                    <ul class="submenu lvl_2">
                        <li><?php echo link_to(__('Taxonomy'),'taxonomy/index');?></li>
                        <li><?php echo link_to(__('Chronostratigraphy'),'chronostratigraphy/index');?></li>
                        <li><?php echo link_to(__('Lithostratigraphy'),'lithostratigraphy/index');?></li>
                        <li><?php echo link_to(__('Lithology'),'lithology/index');?></li>
                        <li><?php echo link_to(__('Mineralogy'),'mineralogy/index');?></li>
                        <li><?php echo link_to(__('Expeditions'),'expedition/index');?></li>
                        <li><?php echo link_to(__('I.G. Numbers'),'igs/index');?></li>
                        <li><?php echo link_to(__('Institutions'),'institution/index');?></li>
                        <li><?php echo link_to(__('People'),'people/index');?></li>
                        <?php if($sf_user->getDbUserType() >= Users::ENCODER) : ?>
                          <li><?php echo link_to(__('Sampling location'),'gtu/index');?></li>
                        <?php endif ; ?>
                        <li><?php echo link_to(__('Collecting Methods'),'methods_and_tools/methodsIndex');?></li>
                        <li><?php echo link_to(__('Collecting Tools'),'methods_and_tools/toolsIndex');?></li>
                        <li><?php echo link_to(__('Expeditions and I.G.'),'expeditionsIgs/index');?></li>
                        <li><?php echo link_to(__('Bibliography'),'bibliography/index');?></li>
                    </ul>
                </li>
                <li><?php echo link_to(__('Specimens'),'specimensearch/index');?></li>
                <li class="pinned_specimens"><?php echo link_to(sprintf(__('Pinned Specimens <i>(%d)</i>'), count($sf_user->getAllPinned('specimen'))),'specimensearch/search?pinned=true&source=specimen');?></li>
                <li><?php echo link_to(__('Collections'),'collection/index');?></li>
                <li><?php echo link_to(__('Loans'),'loan/index');?></li>
            </ul>
        </li>
        <?php if($sf_user->isAtLeast(Users::ENCODER)) : ?>
        <li>
            <a href="#" class="subtitle"><?php echo __('Add');?></a>
            <ul class="submenu">
                <li>
                    <a href="#" class="subtitle"><?php echo __('Catalogues');?> »</a>
                    <ul class="submenu lvl_2">
                        <li><?php echo link_to(__('Taxonomy'),'taxonomy/new');?></li>
                        <li><?php echo link_to(__('Chronostratigraphy'),'chronostratigraphy/new');?></li>
                        <li><?php echo link_to(__('Lithostratigraphy'),'lithostratigraphy/new');?></li>
                        <li><?php echo link_to(__('Lithology'),'lithology/new');?></li>
                        <li><?php echo link_to(__('Mineralogy'),'mineralogy/new');?></li>
                        <li><?php echo link_to(__('Expeditions'),'expedition/new');?></li>
                        <li><?php echo link_to(__('RBINS I.G. Numbers'),'igs/new');?></li>
                        <li><?php echo link_to(__('Institutions'),'institution/new');?></li>
                        <li><?php echo link_to(__('People'),'people/new');?></li>
                        <li><?php echo link_to(__('Sampling location'),'gtu/new');?></li>
                        <li><?php echo link_to(__('Collecting Methods'),'methods_and_tools/new?notion=method');?></li>
                        <li><?php echo link_to(__('Collecting Tools'),'methods_and_tools/new?notion=tool');?></li>
                        <li><?php echo link_to(__('Bibliography'),'bibliography/new');?></li>
                    </ul>
                </li>
                <li><?php echo link_to(__('Specimens'),'specimen/new');?></li>
                <?php if($sf_user->isAtLeast(Users::MANAGER)) : ?>
                <li><?php echo link_to(__('Collections'),'collection/new');?></li>
                <?php endif ?>
                <li><?php echo link_to(__('Loans'),'loan/new');?></li>
            </ul>
        </li>
        <?php endif ?>
        <?php if($sf_user->isAtLeast(Users::ENCODER) ): ?>
        <li>
            <a href="" class="subtitle"><?php echo __('Administration');?></a>
            <ul class="submenu">
                <li><?php echo link_to(__('Mass Actions'),'massactions/index');?></li>
                <li><?php echo link_to(__('Import'),'import/index');?></li>
                <?php if($sf_user->isAtLeast(Users::ADMIN) ): ?>
                  <li><?php echo link_to(__('Big Brother'),'bigbro/index');?></li>
                <?php endif ; ?>
                <?php if($sf_user->isAtLeast(Users::MANAGER) ): ?>
                  <li>
                    <a href="#" class="subtitle"><?php echo __('User');?> »</a>
                    <ul class="submenu lvl_2">
                      <li><?php echo link_to(__('Add'),'user/new');?></li>
                      <li><?php echo link_to(__('Search'),'user/index');?></li>
                    </ul>
                  </li>
                <?php endif ?>
            </ul>
        </li>
        <?php endif ?>
        <li>
            <a href="" class="subtitle"><?php echo __('Help');?></a>
            <ul class="submenu">
                <li><?php echo link_to(__('Help'),'help/index');?></li>
                <li><?php echo link_to(__('Contacts'),'help/contact');?></li>
                <li><?php echo link_to(__('Contribute'),'help/contrib');?></li>
                <li><?php echo link_to(__('About'),'help/about');?></li>
            </ul>
        </li>
        <li class="exit" ><?php echo link_to(image_tag('exit.png', 'alt=Exit'),'account/logout');?></li>
    </ul>
</div>

<script  type="text/javascript">
$(document).ready(function()
{
   $('#navigation').delegate('a.subtitle', 'mouseover', function(event) {
      var self = $(this),
         qtip = '.qtip.ui-tooltip',
         container = $(event.delegateTarget || event.liveFired),
         submenu = self.next('ul'),

      // Determine whether this is a top-level menu
      isTopMenu = self.parents(qtip).length < 1;

      // If it's not a top level and we can't find a sub-menu... return
      if(isTopMenu && !submenu.length) { return false; }
      /*
       * Top-level menus will be placed below the menu item, all others
       * will be placed to the right of each other, top aligned.
       */
      position = isTopMenu ?
         { my: 'top center', at: 'bottom center' } :
         { my: 'top left', at: 'right top' }

      // Create the tooltip
      self.qtip({
         overwrite: false, // Make sure we only render one tooltip
         content: {
            text: self.next('ul') // Use the submenu as the qTip content
         },
         position: $.extend(true, position, {
            // Append the nav tooltips to the #navigation element (see show.solo below)
            container: container,
            // We'll make sure the menus stay visible by shifting/flipping them back into the viewport
            viewport: $(window), adjust: { method: 'shift flip', y: isTopMenu ? -5 : 0}
         }),
         show: {
            event: event.type, // Make sure to sue the same event as above
            ready: true, // Make sure it shows on first mouseover

            /*
             * If it's a top level menu, make sure only one is shown at a time!
             * We'll pass the container element through too so it doesn't hide
             * tooltips unrelated to the menu itself
             */
            solo: isTopMenu ? container : false
         },
         hide: {
            delay: 100,
            event: 'unfocus mouseleave',
            fixed: true // Make sure we can interact with the qTip by setting it as fixed
         },
         style: {
            classes: 'ui-tooltip-nav', // Basic styles
            tip: isTopMenu // We don't want a tip... it's a menu duh!
         },
         events: {
            // Toggle an active class on each menus activator
            toggle: function(event, api) {
               api.elements.target.toggleClass('active', event.type === 'tooltipshow');
            }
         }
      });
   });
});
</script>
