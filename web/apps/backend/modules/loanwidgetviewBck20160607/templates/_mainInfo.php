<table class="table_main_info">
  <tbody>
    <tr>
      <th><?php echo __('Name');?> :</th>
      <td>
        <?php echo $loan->getName();?>
      </td>
      <th><?php echo __('Starts on');?> :</th>
      <td>
        <?php $date = new DateTime($loan->getFromDate());
                echo $date->format('d/m/Y'); ?>
      </td>
      <th></th>
      <td></td>
    </tr>

    <tr>
      <th></th>
      <td></td>

      <th><?php echo __('Ends on');?> :</th>
      <td><?php $date = new DateTime($loan->getToDate());
                echo $date->format('d/m/Y'); ?>
      </td>

      <th><?php echo __('Extended to date');?> :</th>
      <td>
        <?php $date = new DateTime($loan->getToDate());
                echo $date->format('d/m/Y'); ?>
        <?php echo $loan->getExtendedToDate();?>
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
