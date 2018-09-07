<table class="catalogue_table_view">
  <thead>
    <tr>
      <th><?php echo __('Community');?></th>
      <th><?php echo __('Names');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody id="property">
    <?php foreach($vernacular_names as $vernacular_name):?>
    <tr>
      <td>
        <?php echo $vernacular_name->getCommunity();?>
      </td>
      <td>
        <?php echo $vernacular_name->getName();?>
      </td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>

