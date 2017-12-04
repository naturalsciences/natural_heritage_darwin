<table class="catalogue_table_view">
  <thead>
    <tr>
      <th><?php echo __('Type');?></th>
      <th><?php echo __('Value');?></th>
      <th><?php echo __('Tags');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($comms as $comm):?>
  <tr>
    <td>
      <?php if($comm->getCommType()=="phone/fax"):?>
	      <?php echo __('Phone');?>
      <?php else:?>
	      <?php echo __('e-Mail');?>      
	    <?php endif ; ?>    
    </td>
    <td>
      <?php echo $comm->getEntry();?>
    </td>
    <td>
      <?php foreach($comm->getTagsAsArray() as $item):?>
	<span class="tag"><?php echo $item;?><?php echo image_tag('tags.gif');?></span>
      <?php endforeach;?>
    </td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>
