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
      <td><?php echo $item->getCategory();?></td>
      <td><?php echo $item->getActionObservation();?></td>
      <td><?php echo $item->getDescription();?></td>
      <td><?php echo $item->People->getFormatedName();?></td>
      <td><?php echo $item->getModificationDateTimeMasked(ESC_RAW);?></td>
      <td><?php echo link_to(image_tag('remove.png', array("title" => __("View"))),'loanitem/delmaintenance?id='.$item->getId(),'class=delete_maint_button');?></td>
    </tr>
  <?php endforeach;?>
</tbody>
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