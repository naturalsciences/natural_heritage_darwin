<!--ftheeten 2018 04 22-->
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
<?php if($flagMenu!="off"):?>
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
            <li><?php echo link_to(' ',$sf_context->getConfiguration()->generatePublicUrl('homepage', array(), $sf_request), 'class="img_drop"');?></li>
            <li><?php echo link_to(' ',$sf_context->getConfiguration()->generatePublicUrl('homepage', array(), $sf_request), 'class="img_DaRWIN"');?></li>
          </ul>
        </td>
        <!--<td></td>-->
      </tr>
      <tr>
        <td colspan="2">
          <ul class="menu_link">
            <li><?php echo link_to(__('Zoological Search'),$sf_context->getConfiguration()->generatePublicUrl('search', array(), $sf_request));?></li>
            <li><?php echo link_to(__('Geo/Paleo Search'),$sf_context->getConfiguration()->generatePublicUrl('geoSearch', array(), $sf_request));?></li>
            <li><?php echo link_to(__('Take a tour'),$sf_context->getConfiguration()->generatePublicUrl('tour', array(), $sf_request));?></li>
            <li><?php echo link_to(__('Contacts'),$sf_context->getConfiguration()->generatePublicUrl('contact', array(), $sf_request));?></li>
          </ul>
        </td>
      </tr>
      <tr>
        <td class="lang_picker"  colspan="2">
          <ul>
            <li><?php echo link_to('En','account/lang?lang=en');?></li>
            <li class="sep">|</li>
            <li><?php echo link_to('Fr','account/lang?lang=fr');?></li>
            <li class="sep">|</li>
            <li><?php echo link_to('Nl','account/lang?lang=nl');?></li>
          </ul>
        </td>
      </tr>
    </table>
    <div class="small_blue_line"></div>
  </div>
</td>
<?php endif;?>