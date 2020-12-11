<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $sf_user->getCulture() ?>" lang="<?php echo $sf_user->getCulture() ?>">
  <head>
  <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8"/>
    <?php include_http_metas() ?>  
    <?php include_metas() ?>
    <?php include_javascripts() ?>
    <?php include_stylesheets() ?>
    <!--ftheeten 2018 05 09-->
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <meta http-equiv="Content-Security-Policy" content="default-src *; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline' 'unsafe-eval' http://www.google.com https://www.gstatic.com  https://www.google-analytics.com http://fonts.gstatic.com">
    <title><?php include_slot('title') ?></title>
    <!--[if IE]>
    <?php echo stylesheet_tag('ie.css') ?>
    <![endif]-->
    <link rel="shortcut icon" href="/favicon.ico" />
  </head>
  <body>
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
    <table class="all_content">
      <tr>
        <?php include_partial('global/head_menu') ?>
      </tr>
      <tr>
        <td class="content">
          <?php echo $sf_content ?>
        </td>
      </tr>
      <?php if($flagMenu!="off" ):?>
        <?php 
        if(array_key_exists("menu", $_SESSION))
        {
            unset($_SESSION['menu']);
        }
        ?>
      <tr>
        <td class="menu_bottom">
          <div class="page">
            <table>
              <tr>
                <td class="browser_img "><ul style="">
                  <li><?php echo __('Recommended browser for DaRWIN :') ; ?></li>
                  <li><?php echo image_tag('chrome.png',array('title' =>'Google Chrome','width'=>'32','height' =>'32'));?></li>
                  <li><?php echo image_tag('firefox.png',array('title' =>'Firefox >= 3.6','width'=>'32','height' =>'32'));?></li>
                  <li><?php echo image_tag('Safari.png',array('title' =>'Safari','width'=>'32','height' =>'32'));?></li>
                </ul></td>
              </tr>
            </table>    
          </div>
        </td>     
      </tr>
       <?php endif;?>
    </table>
    <?php if(sfConfig::get('dw_broadcast_enabled', false)):?>
      <div id="broadcast_bottom_padding"></div>
      <div id="broadcast_bottom"><?php echo __(sfConfig::get('dw_broadcast_message', ''));?>
        <span><?php echo __(sfConfig::get('dw_broadcast_submessage', ''));?></span>
      </div>
    <?php endif;?>
    <?php if(sfConfig::get('dw_analytics_enabled', false)):?>
			<?php include_partial('global/analytics') ?>
    <?php endif;?>
  </body>
</html>
