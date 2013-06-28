<table class="catalogue_table_view">
  <tr>
  <th class="top_aligned"><?php echo __('Specimen part');?></th>
  <td>
    <?php echo $spec->getSpecimenPart();?>
  </td>
  </tr>
  <tr>
  <th class="top_aligned"><?php echo __('Object name');?></th>
  <td>
    <?php echo $spec->getObjectName();?>
  </td>
  </tr>
  <tr>
  <th class="top_aligned"><?php echo __('Category');?></th>
  <td>
    <?php echo $spec->getCategory();?>
  </td>
  </tr>
</table>
