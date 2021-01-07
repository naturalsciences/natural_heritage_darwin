<script>
 //ftheeten 2018 05 30

    function disableFrameMenu() 
    {
        var in_frame=false;
        var fame_tested=false;
        var this_frame=null;
        var ancestor_frame=null;
        var parentUrl = null;
        var redirect_mode=false;
      
        
        $.ajax({
                  url: detect_https("http://<?php print(parse_url(sfContext::getInstance()->getRequest()->getUri(),PHP_URL_HOST ));?>/search/checkReferer"),              
                }).done(
                
                    function(data) 
                    {
                      
                      var tmp_url=data["DW_REFERER"];                     
                      if(tmp_url.length>0)
                      {
                          if(tmp_url.indexOf('<?php print(sfConfig::get('dw_domain_disable_menu'));?>') !==-1)
                          {
                            in_frame=true;
                           
                           }
                      }
                       if(in_frame)
                       {
                          
                             $.ajax({
                            url: detect_https("http://<?php print(parse_url(sfContext::getInstance()->getRequest()->getUri(),PHP_URL_HOST ));?>/search/disableMenu?menu=off"),              
                                }).done(function() {
                            
                                });
                       }
                       fame_tested=true;
                    }
                );
    
     
   
        

   
    
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
    
	$referer_domain=parse_url($_SERVER["HTTP_REFERER"])["scheme"].'://'.parse_url($_SERVER["HTTP_REFERER"])["host"];
    $_SESSION['DW_REFERER']="";
    if(array_key_exists("menu", $_REQUEST))
    {       
        if($_REQUEST['menu']=="off")
        {
          
            $flagMenu="off";
            $_SESSION['DW_REFERER']=$referer_domain;
            $_SESSION['menu']= $flagMenu;  
        }
        else
        {
             $_SESSION['menu']= "on";  
        }
    }
    elseif(array_key_exists("menu", $_SESSION))
    {      
        if($_SESSION['menu']=="off")
        {         
            $flagMenu="off";          
        }
        
    }
    
    //$_SESSION['menu']= $flagMenu;  
?>
<?php if($flagMenu!="off" ):?>

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
                <li><?php echo link_to(__('Reports'),'report/index');?></li>
            </ul>
        </li>
        <li>
            <a href="#" class="subtitle"><?php echo __('Searches');?></a>
            <ul class="submenu">
                <li>
                    <a href="#" class="subtitle"><?php echo __('Catalogues');?> »</a>
                    <ul class="submenu lvl_2">
                        <li><?php echo link_to(__('Taxonomy'),'taxonomy/index');?></li>
                        <!--ftheeten 2017 07 17--!>
                        <li><?php echo link_to(__('Taxonomic groups'),'taxonomymetadata/index');?></li>
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
						  <li><?php echo link_to(__('Georeferences'),'georeferences/index');?></li>
                        <?php endif ; ?>
                        <li><?php echo link_to(__('Collecting Methods'),'methods_and_tools/methodsIndex');?></li>
                        <li><?php echo link_to(__('Collecting Tools'),'methods_and_tools/toolsIndex');?></li>                        
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
                        <li><?php echo link_to(__('Taxonomy'),'taxonomy/new');?></li>
                         <!--ftheeten 2017 07 17--!>
                        <li><?php echo link_to(__('Taxonomic groups'),'taxonomymetadata/new');?></li>
                        <li><?php echo link_to(__('Chronostratigraphy'),'chronostratigraphy/new');?></li>
                        <li><?php echo link_to(__('Lithostratigraphy'),'lithostratigraphy/new');?></li>
                        <li><?php echo link_to(__('Lithology'),'lithology/new');?></li>
                        <li><?php echo link_to(__('Mineralogy'),'mineralogy/new');?></li>
                        <li><?php echo link_to(__('Expeditions'),'expedition/new');?></li>
                        <li><?php echo link_to(__('RBINS I.G. Numbers'),'igs/new');?></li>
                        <li><?php echo link_to(__('Institutions'),'institution/new');?></li>
                        <li><?php echo link_to(__('People'),'people/new');?></li>
                        <li><?php echo link_to(__('Sampling location'),'gtu/new');?></li>
						<li><?php echo link_to(__('Georeferences'),'georeferences/new');?></li>
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
				<li><?php echo link_to(__('Collection statistics'),'collection/statistics');?></li>
                <li>
                    <a href="#" class="subtitle"><?php echo __('Import');?> »</a>
                    <ul class="submenu lvl_2">
                        <li><?php echo link_to(__('Specimens'),'import/index');?></li>
                        <li><?php echo link_to(__('Taxonomy'),'import/indexTaxon');?></li>
                        <li><?php echo link_to(__('Lithostratigraphy'),'import/indexLithostratigraphy');?></li>
						<li><?php echo link_to(__('Localities'),'import/indexLocalities');?></li>
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

