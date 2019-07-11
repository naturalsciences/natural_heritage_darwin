<table class="catalogue_table_view">
  <tbody>
    <tr>
      <th>
        <?php echo __('Acquisition category'); ?>
      </th>
      <td>
        <?php echo $spec->getAcquisitionCategory() ; ?>
      </td>
    </tr>
    <tr>
      <th>
        <?php echo __('Acquisition date') ?>
      </th>
      <td class="datesNum">
        <?php echo $spec->getAcquisitionDateMasked(ESC_RAW); ?>
      </td>
    </tr>
  </tbody>
</table>
