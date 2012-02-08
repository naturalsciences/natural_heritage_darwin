<table class="table_main_info">
  <tbody>
    <tr>
      <th><?php echo __('Name');?> :</th>
      <td>
        <?php echo $loan->getName();?>
      </td>
      <th><?php echo __('Start On');?> :</th>
      <td>
        <?php echo $loan->getFromDate();?>
      </td>

      <th><?php echo __('Extended to');?> :</th>
      <td>
        <?php echo $loan->getExtendedToDate();?>
      </td>
    </tr>

    <tr>
      <th></th>
      <td></td>

      <th><?php echo __('Ends On');?> :</th>
      <td>
        <?php echo $loan->getToDate();?>
      </td>

      <th><?php echo __('Effective to');?> :</th>
      <td>
        <?php echo $loan->getEffectiveToDate();?>
      </td>
    </tr>

    <tr>
      <th></th>
      <td colspan="5">&nbsp;</td>
    </tr>

    <tr>
      <th><?php echo __('Description ');?> :</th>
      <td colspan="5">
        <?php echo $loan->getDescription();?>
      </td>
    </tr>
  </tbody>
</table>