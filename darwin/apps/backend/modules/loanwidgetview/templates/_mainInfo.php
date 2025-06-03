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
	<tr>
      <th><?php echo __('Collection manager ');?> :</th>
      <td >
        <?php echo $loan->getCollectionManager();?>
      </td>
    </tr>
	<tr>
      <th><?php echo __('Collection manager ');?> :</th>
      <td >
        <?php echo $loan->getCollectionManagerTitle();?> <?php echo $loan->getCollectionManager();?>
      </td>
    </tr>
	<tr>
      <th><?php echo __('Collection manager mail ');?> :</th>
      <td >
        <?php echo $loan->getCollectionManagerMail();?>
      </td>
    </tr>
	<tr>
      <th><?php echo __('Collection manager phone ');?> :</th>
      <td >
        <?php echo $loan->getCollectionManagerPhone();?>
      </td>
    </tr>
  </tbody>
</table>
