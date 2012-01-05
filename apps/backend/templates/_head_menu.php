<div class="menu_top">
    <ul class="sf-menu main_menu">
        <li class="house"><?php echo link_to(image_tag('home.png', 'alt=Home'),'board/index');?></li>
        <li>
            <a href="#"><?php echo __('My Preferences');?></a>
            <ul>
              <li><?php echo link_to(__('My Profile'),'user/profile');?></li>
              <li><?php echo link_to(__('My Widgets'),'user/widget');?></li>
              <li><?php echo link_to(__('My Preferences'),'user/preferences');?></li>
              <li><?php echo link_to(__('Saved Specimens list'),'savesearch/index?specimen=1');?></li>
              <li><?php echo link_to(__('Saved search'),'savesearch/index');?></li>
            </ul>
        </li>
        <li>
            <a href="#"><?php echo __('Searches');?></a>
            <ul>
                <li>
                    <a href="#"><?php echo __('Catalogues');?></a>
                    <ul>
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
                        <li><?php echo link_to(__('Expeditions and Igs'),'expeditionsIgs/index');?></li> 
                    </ul>
                </li>
                <li><?php echo link_to(__('Specimens'),'specimensearch/index');?></li>
                <li>
                  <a href="#"><?php echo __('Pinned Items');?></a>
                  <ul>
                    <li><?php echo link_to(sprintf(__('Specimens (%d)'), count($sf_user->getAllPinned('specimen'))),'specimensearch/search?pinned=true&source=specimen');?></li>
                    <li><?php echo link_to(sprintf(__('Individuals (%d)'), count($sf_user->getAllPinned('individual'))),'specimensearch/search?pinned=true&source=individual');?></li>
                    <li><?php echo link_to(sprintf(__('Parts (%d)'), count($sf_user->getAllPinned('part'))),'specimensearch/search?pinned=true&source=part');?></li>
                  </ul>
                </li>

                <li><?php echo link_to(__('Collections'),'collection/index');?></li>
            </ul>
        </li>
        <?php if($sf_user->isAtLeast(Users::ENCODER)) : ?>
        <li>
            <a href="#"><?php echo __('Add');?></a>
            <ul>
                <li>
                    <a href="#"><?php echo __('Catalogues');?></a>
                    <ul>
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
                    </ul>
                </li>
                <li><?php echo link_to(__('Specimens'),'specimen/new');?></li>
                <?php if($sf_user->isAtLeast(Users::MANAGER)) : ?>
                <li><?php echo link_to(__('Collections'),'collection/new');?></li>
                <?php endif ?>
            </ul>
        </li>
        <?php endif ?>
        <?php if($sf_user->isAtLeast(Users::ENCODER) ): ?>
        <li>
            <a href=""><?php echo __('Administration');?></a>
            <ul>
                <li><?php echo link_to(__('Mass Actions'),'massactions/index');?></li>
                <li><?php echo link_to(__('Import'),'import/index');?></li>
                <?php if($sf_user->isAtLeast(Users::ADMIN) ): ?>
                  <li><?php echo link_to(__('Big Brother'),'bigbro/index');?></li>
                <?php endif ; ?>
                <?php if($sf_user->isAtLeast(Users::MANAGER) ): ?>
                  <li>
                    <a href="#"><?php echo __('User');?></a>
                    <ul>
                      <li><?php echo link_to(__('Add'),'user/new');?></li>
                      <li><?php echo link_to(__('Search'),'user/index');?></li>
                    </ul>
                  </li>
                <?php endif ?>
            </ul>
        </li>
        <?php endif ?>
        <li>
            <a href=""><?php echo __('Help');?></a>
            <ul>
                <li><?php echo link_to(__('Help'),'help/index');?></li>                
                <li><?php echo link_to(__('Contacts'),'help/contact');?></li>
                <li><?php echo link_to(__('Contribute'),'help/contrib');?></li>
                <li><?php echo link_to(__('About'),'help/about');?></li>
            </ul>
        </li>
        <li class="exit" ><?php echo link_to(image_tag('exit.png', 'alt=Exit'),'account/logout');?></li>
    </ul>
</div>

<?php if($sf_user->getPreference('gtu_google_activated', true)):?>
  <script src="http://maps.google.com/maps/api/js?v=3.3&amp;sensor=false"></script>
<?php endif;?>

<script  type="text/javascript">
 var with_gmap= <?php echo $sf_user->getPreference('gtu_google_activated', true) ? 'true' : 'false';?>;
$(document).ready(function () {
  o = {"dropShadows":false, "autoArrows":true,"delay":400};
  $('ul.main_menu').supersubs().superfish(o);
  $('ul.main_menu > li:not(.house):not(.exit)').append('<img class="highlight" src="/images/menu_expand.png" alt="" />');
});
</script>
