<table class="catalogue_table">
  <thead>
    <tr>
      <th><?php echo __('Country');?></th>
      <th><?php echo __('Region');?></th>
      <th><?php echo __('Locality');?></th>
      <th><?php echo __('Address');?></th>
      <th><?php echo __('Tags');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($addresses as $address):?>
  <tr>
    <td> 
      <a class="link_catalogue" title="<?php echo __('Edit Address');?>"  href="<?php echo url_for('people/address?ref_id='.$eid.'&id='.$address->getId());?>">
	<?php echo $address->getCountry();?>
      </a>
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
    <td class="widget_row_delete">     
      <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=people_addresses&id='.$address->getId());?>" title="<?php echo __('Are you sure ?') ?>"><?php echo image_tag('remove.png'); ?>
      </a>
    </td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>

<br />
<?php echo image_tag('add_green.png');?>
<a title="<?php echo __('Add Address');?>" class="link_catalogue" href="<?php echo url_for('people/address?ref_id='.$eid);?>"><?php echo __('Add');?></a>
