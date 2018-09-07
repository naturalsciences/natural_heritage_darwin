<table class="catalogue_table">
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
        <a class="link_catalogue" title="<?php echo __('Edit Vernacular Names');?>" href="<?php echo url_for('vernacularnames/vernacularnames?table='.$table.'&id='.$eid); ?>">
          <?php echo $vernacular_name->getCommunity();?>
        </a>
      </td>
      <td>
        <?php echo $vernacular_name->getName();?>
      </td>
      <td class="widget_row_delete">
        <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=vernacular_names&id='.$vernacular_name->getId());?>" title="<?php echo __('Delete Vernacular Names') ?>"><?php echo image_tag('remove.png'); ?>
        </a>
      </td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>

<script type="text/javascript">
$('.display_value').click(showValues);
$('.hide_value').click(hideValues);
</script>
<br />
<?php echo image_tag('add_green.png');?><a title="<?php echo __('Add Names');?>" class="link_catalogue" href="<?php echo url_for('vernacularnames/vernacularnames?table='.$table.'&id='.$eid); ?>"><?php echo __('Add');?></a>
