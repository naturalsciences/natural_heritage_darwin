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
<td class="header_menu">
  <div class="menu_top">
    <table>
      <tr class="menu_header_image">
        <td colspan="2">
          <ul id="header_map">
            <li><?php echo link_to(' ','@homepage', 'class="img_drop"');?></li>
            <li><?php echo link_to(' ','@homepage', 'class="img_DaRWIN"');?></li>
          </ul>
        </td>
        <!--<td></td>-->
      </tr>
      <tr>
        <td colspan="2">
          <ul class="menu_link">
            <li><?php echo link_to(__('Zoological Search'),'@search');?></li>
            <li><?php echo link_to(__('Geo/Paleo Search'),'@geoSearch');?></li>
            <li><?php echo link_to(__('Take a tour'),'@tour');?></li>
            <li><?php echo link_to(__('Contacts'),'@contact');?></li>
          </ul>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="lang_picker"><ul style="">
          <li><?php echo link_to('En','board/lang?lang=en');?></li>
          <li class="sep">|</li>
          <li><?php echo link_to('Fr','board/lang?lang=fr');?></li>
          <li class="sep">|</li>
          <li><?php echo link_to('Nl','board/lang?lang=nl');?></li>
          <li class="sep">|</li>
          <li><?php echo link_to('Es','board/lang?lang=es_ES');?></li>
        </ul></td>
      </tr>
    </table>
  </div>

  <?php include_component('login','MenuLogin') ; ?>

</td>
<?php endif;?>   