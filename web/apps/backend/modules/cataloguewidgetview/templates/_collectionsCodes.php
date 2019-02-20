<table class="catalogue_table_view">
  <thead>
    <tr>
      <th><?php echo __('Code prefix');?></th>
      <th class="centered"><?php echo __('Code prefix sep.');?></th>
      <th class="centered"><?php echo __('Code suffix sep.');?></th>
      <th><?php echo __('Code suffix');?></th>
      <th class="centered"><?php echo __('Auto incremented ?');?></th>
      <th class="centered"><?php echo __('...for new spec. only ?');?></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>
        <?php echo $collCodes->getCodePrefix();?>
      </td>
      <td class="centered">
        <?php echo $collCodes->getCodePrefixSeparator();?>
      </td>
      <td class="centered">
        <?php echo $collCodes->getCodeSuffixSeparator();?>
      </td>
      <td>
        <?php echo $collCodes->getCodeSuffix();?>
      </td>
      <td class="centered">
        <?php echo ($collCodes->getCodeAutoIncrement())?image_tag('checkbox_checked_green.png'):image_tag('checkbox_unchecked_green.png'); ?>
      </td>
      <td class="centered">
        <?php echo ($collCodes->getCodeAutoIncrementForInsertOnly())?image_tag('checkbox_checked_green.png'):image_tag('checkbox_unchecked_green.png'); ?>
      </td>
    </tr>
  </tbody>
</table>
