<script>
 //ftheeten 2018 05 30
    function disableFrameMenu() {
    var isInIframe = (parent !== window),
        parentUrl = null;

        if (isInIframe) {
        
            parentUrl = document.referrer;
            if(parentUrl.indexOf('<?php print(sfConfig::get('dw_domain_disable_menu'));?>') ===-1)
            {
                $.ajax({
                  url: "http://<?php print(parse_url(sfContext::getInstance()->getRequest()->getUri(),PHP_URL_HOST ));?>/search/disableMenu?menu=on",              
                }).done(function() {
                  
                });
            }
            
        }
        else
        {
            console.log("try");
             console.log("http://<?php print(parse_url(sfContext::getInstance()->getRequest()->getUri(),PHP_URL_HOST ));?>/search/disableMenu?menu=on");
             $.ajax({
                  url: "http://<?php print(parse_url(sfContext::getInstance()->getRequest()->getUri(),PHP_URL_HOST ));?>/search/disableMenu?menu=on",              
                }).done(function() {
                  
                });
        }

    
    }
    (function($){ //create closure so we can safely use $ as alias for jQuery

      $(document).ready(function(){

        //ftheeten 2018 05 30
        disableFrameMenu();
        
        
      });

    })(jQuery);
    
    //ftheeten 2018 05 30
    
    
</script>

<?php
    $flagMenu="on";
    
    
    if(array_key_exists("menu", $_REQUEST))
    {       
        if($_REQUEST['menu']=="off")
        {
            $flagMenu="off";
        }
    }
    elseif(array_key_exists("menu", $_SESSION))
    {       
        if($_SESSION['menu']=="off")
        {
            $flagMenu="off";
        }
        
    }
    $_SESSION['menu']= $flagMenu;  
?>
<?php if($flagMenu!="off" ):?>
<?php 
if(array_key_exists("menu", $_SESSION))
{
    unset($_SESSION['menu']);
}
?>
<div class="menu_top">
    <ul id="navigation" class="sf-menu">
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
					    <!--JMHerpers 2018 03 08--!>
                        <li><?php echo link_to(__('Taxons'),'taxonomy/index');?></li>
                        <!--ftheeten 2017 07 17--!>
                        <li><?php echo link_to(__('Taxonomies'),'taxonomymetadata/index');?></li>
						<!--JMHerpers 2018 03 08--!>
                        <!--<li><?php echo link_to(__('Chronostratigraphy'),'chronostratigraphy/index');?></li>
                        <li><?php echo link_to(__('Lithostratigraphy'),'lithostratigraphy/index');?></li>
                        <li><?php echo link_to(__('Lithology'),'lithology/index');?></li>
                        <li><?php echo link_to(__('Mineralogy'),'mineralogy/index');?></li>-->
                        <li><?php echo link_to(__('Expeditions'),'expedition/index');?></li>
                        <li><?php echo link_to(__('I.G. Numbers'),'igs/index');?></li>
                        <li><?php echo link_to(__('Institutions'),'institution/index');?></li>
                        <li><?php echo link_to(__('People'),'people/index');?></li>
                        <?php if($sf_user->getDbUserType() >= Users::ENCODER) : ?>
                          <li><?php echo link_to(__('Sampling location'),'gtu/index');?></li>
                        <?php endif ; ?>
                        <li><?php echo link_to(__('Collecting Methods'),'methods_and_tools/methodsIndex');?></li>
						<!--JMHerpers 2018 02 20 remove tool menu-->
                        <!--<li><?php echo link_to(__('Collecting Tools'),'methods_and_tools/toolsIndex');?></li>-->
                        <li><?php echo link_to(__('Expeditions and I.G.'),'expeditionsIgs/index');?></li>
                        <li><?php echo link_to(__('Bibliography'),'bibliography/index');?></li>
                        <li><?php echo link_to(__('Comments'),'comment/index');?></li>
                        <li><?php echo link_to(__('Properties'),'property/index');?></li>
                        <li><?php echo link_to(__('Multimedia'),'multimedia/index');?></li>

                    </ul>
                </li>
                <li><?php echo link_to(__('Specimens'),'specimensearch/index');?></li>
                <li class="pinned_specimens"><?php echo link_to(sprintf(__('Pinned Specimens <i>(%d)</i>'), count($sf_user->getAllPinned('specimen'))),'specimensearch/search?pinned=true&source=specimen');?></li>
                <li><?php echo link_to(__('Collections'),'collection/index');?></li>
                <li><?php echo link_to(__('Loans'),'loan/index');?></li>
                <li><?php echo link_to(__('Storage Search'),'storage/index');?></li>
            </ul>
        </li>
        <?php if($sf_user->isAtLeast(Users::ENCODER)) : ?>
        <li>
            <a href="#" class="subtitle"><?php echo __('Add');?></a>
            <ul class="submenu">
                <li>
                    <a href="#" class="subtitle"><?php echo __('Catalogues');?> »</a>
                    <ul class="submenu lvl_2">
					    <!--JMHerpers 2018 03 08-->
                        <li><?php echo link_to(__('Taxons'),'taxonomy/new');?></li>
                         <!--ftheeten 2017 07 17--!>
                        <li><?php echo link_to(__('Taxonomies'),'taxonomymetadata/new');?></li>
						<!--JMHerpers 2018 03 08-->
                        <!--<li><?php echo link_to(__('Chronostratigraphy'),'chronostratigraphy/new');?></li>
                        <li><?php echo link_to(__('Lithostratigraphy'),'lithostratigraphy/new');?></li>
                        <li><?php echo link_to(__('Lithology'),'lithology/new');?></li>
                        <li><?php echo link_to(__('Mineralogy'),'mineralogy/new');?></li>-->
                        <li><?php echo link_to(__('Expeditions'),'expedition/new');?></li>
                        <li><?php echo link_to(__('RBINS I.G. Numbers'),'igs/new');?></li>
                        <li><?php echo link_to(__('Institutions'),'institution/new');?></li>
                        <li><?php echo link_to(__('People'),'people/new');?></li>
                        <li><?php echo link_to(__('Sampling location'),'gtu/new');?></li>
                        <li><?php echo link_to(__('Collecting Methods'),'methods_and_tools/new?notion=method');?></li>
						<!--JMHerpers 2018 02 20 remove tool menu-->
                        <!--<li><?php echo link_to(__('Collecting Tools'),'methods_and_tools/new?notion=tool');?></li>-->
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
               <li>
                    <a href="#" class="subtitle"><?php echo __('Import');?> »</a>
                    <ul class="submenu lvl_2">
                        <li><?php echo link_to(__('Specimens'),'import/index');?></li>
                        <li><?php echo link_to(__('Taxonomy'),'import/indexTaxon');?></li>
                    </ul>
                </li>
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
<?php else:?>    
    <style>
        .widget_collection_global {
    
            top: 0px;
            z-index: 1100;
        }
    </style>    
<?php endif;?>
<script>

    (function($){ //create closure so we can safely use $ as alias for jQuery

      $(document).ready(function(){

        // initialise plugin
        var example = $('#navigation').superclick({
          cssArrows: false
          //add options here if required
        });
        $('.lvl_2').hide();
      });

    })(jQuery);
</script>
