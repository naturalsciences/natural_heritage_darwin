<table class="catalogue_table_view">
  <tbody>
    <tr>
      <th>
        <?php echo __('Gathering date from'); ?>
      </th>
      <td class="datesNum">
        <?php echo $spec->getGtuFromDateMasked(ESC_RAW) ; ?>
      </td>
    </tr>
    <tr>
      <th>
        <?php echo __('Gathering date to') ?>
      </th>
      <td class="datesNum">
        <?php echo $spec->getGtuToDateMasked(ESC_RAW) ; ?>
      </td>
    </tr>
  </tbody>
</table>
 
