<table class="catalogue_table_view">
<thead>
  <tr>
    <th><?php echo __('Type') ;?></th>
    <th><?php echo __('Title');?></th>
    <th><?php echo __('Details') ;?></th>
    <th><?php echo __('Person') ;?></th>
    <th><?php echo __('Date') ;?></th>
    <th></th>
  </tr>
</thead>
<tbody>
  <?php foreach($maintenances as $item):?>
    <tr>
      <td><?php echo __($item->getCategory());?></td>
      <td><?php echo __($item->getActionObservation());?></td>
      <td><?php echo $item->getDescription();?></td>
      <td><?php echo $item->People->getFormatedName();?></td>
      <td><?php echo $item->getModificationDateTimeMasked(ESC_RAW);?></td>
      <td>
<?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'maintenances/view?id='.$item->getId(),'target=_blank');?>   
   <?php echo link_to(image_tag('edit.png', array("title" => __("Edit"))),'maintenances/edit?id='.$item->getId(),'target=_blank');?> 
      <?php echo link_to(image_tag('remove.png', array("title" => __("Delete"))),'maintenances/delmaintenance?id='.$item->getId(),'class=delete_maint_button');?></td>
    </tr>
  <?php endforeach;?>
</tbody>
<tfoot>
  <tr>
    <td colspan="6"><a href="<?php echo url_for('maintenances/new?table=loan_items&record_id='.$loan_item->getId()); ?>" target="_blank"><?php echo __("Add maintenance for this item") ; ?></a></td>
  </tr>
</tfoot>
</table>
<script type="text/javascript">
  $(document).ready(function () {
    $('.delete_maint_button').click(function(event)
    {
      event.preventDefault();
      el = $(this);
      $.ajax({
        url: el.attr('href'),
        success: function(html){
          el.closest('tr').hide();
        }
      });
    });
  });
</script>
