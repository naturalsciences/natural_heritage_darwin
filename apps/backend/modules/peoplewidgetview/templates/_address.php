<table class="catalogue_table_view">
  <thead>
    <tr>
      <th><?php echo __('Country');?></th>
      <th><?php echo __('Region');?></th>
      <th><?php echo __('Locality');?></th>
      <th><?php echo __('Address');?></th>
      <th><?php echo __('Tags');?></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($addresses as $address):?>
  <tr>
    <td>
     	<?php echo $address->getCountry();?>     
    </td>
    <td>
      <?php echo $address->getRegion();?>
    </td>
    <td>   
      <?php echo $address->getLocality();?>
      <?php echo $address->getZipCode();?>
    </td>
    <td>
      <?php echo $address->getEntry();?>
      <?php echo $address->getPoBox();?>
      <?php echo $address->getExtendedAddress();?>
    </td>
    <td>
      <?php foreach($address->getTagsAsArray() as $item):?>
	<span class="tag"><?php echo $item;?><?php echo image_tag('tags.gif');?></span>
      <?php endforeach;?>
    </td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>
