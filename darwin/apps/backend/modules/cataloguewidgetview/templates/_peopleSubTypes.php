
<table class="catalogue_table">
  <thead>
    <tr>
      <th><?php echo __('Sub type');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($sub_types as $sub_type):?>
  <tr>
    <td>
      
	    <?php print($sub_type->getSubType());?>
    
    </td>
    
  </tr>
  <?php endforeach;?>
  </tbody>
</table>

