<div id="lat_long_set">
  <p><strong><?php echo __('Choose latitude/longitude on map');?></strong><input type="checkbox" id="show_as_map"></p>
  <br /><br />
  <table>
    <tr>
      <td>
      </td>
      <th>
        <?php echo $form['lat_from']->renderLabel();?>
      </th>
      <th>
        <?php echo $form['lon_from']->renderLabel();?>
      </th>
    </tr>
    <tr>
      <th class="right_aligned"><?php echo __('Between');?></th>
      <td><?php echo $form['lat_from'];?></td>
      <td><?php echo $form['lon_from'];?><?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?></td>
    </tr>
    <tr>
      <th class="right_aligned"><?php echo __('And');?></th>
      <td><?php echo $form['lat_to'];?></td>
      <td><?php echo $form['lon_to'];?><?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?></td>
    </tr>
  </table>
</div>
  <div id="map_search_form" class="hidden">
    <div style="width: 600px; height:400px" id="smap"></div>
  </div>


<script  type="text/javascript">
    //var results;
    initSearchMap();

</script>
