<table class="catalogue_table">
  <thead>
    <tr>
      <th><?php echo __('Code prefix');?></th>
      <th class="centered"><?php echo __('Code prefix sep.');?></th>
      <th class="centered"><?php echo __('Code suffix sep.');?></th>
      <th><?php echo __('Code suffix');?></th>
      <th class="centered"><?php echo __('Auto incremented ?');?></th>
      <th class="centered"><?php echo __('...for new spec. only ?');?></th>
      <th class="centered"><?php echo __('Duplicate specimen codes');?></th>
      <th class="centered"><?php echo __('Code mask');?></th>
      <th></th>
      <th></th>
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
      <td class="centered">
        <?php echo ($collCodes->getCodeSpecimenDuplicate())?image_tag('checkbox_checked_green.png'):image_tag('checkbox_unchecked_green.png'); ?>
      </td>
      <td class="centered">
        <?php echo $collCodes->getCodeMask();?>
      </td>
      <td class="widget_row_delete">
        <a class="link_catalogue" title="<?php echo __('Edit default specimen codes prefix and suffix');?>" href="<?php echo url_for('collection/addSpecCodes?id='.$eid); ?>">
          <?php echo image_tag('edit.png'); ?>
        </a>
      </td>
      <td class="widget_row_delete">
        <a class="widget_row_delete" href="<?php echo url_for('collection/deleteSpecCodes?id='.$eid);?>" title="<?php echo __('Are you sure ?') ?>"><?php echo image_tag('remove.png'); ?>
        </a>
      </td>
    </tr>
  </tbody>
</table>
