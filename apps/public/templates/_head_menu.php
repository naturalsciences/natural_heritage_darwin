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
