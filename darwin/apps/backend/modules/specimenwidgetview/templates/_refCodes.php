<table  class="catalogue_table_view">
  <thead style="<?php echo ($Codes->count()?'':'display: none;');?>">
    <tr>
      <th>
        <?php echo __('Category'); ?>
      </th>
      <th>
        <?php echo __('Code') ; ?>
      </th>
    </tr>
  </thead>
  <?php foreach($Codes as $code):?>
  <tr>
    <td><?php echo $code->getCodeCategory();?></td>
    <td>
      <?php echo $code->getCodePrefix();?>
      <?php echo $code->getCodePrefixSeparator();?>
      <?php echo $code->getCode();?>
      <?php echo $code->getCodeSuffixSeparator();?>
      <?php echo $code->getCodeSuffix();?>
    </td>
  </tr>
  <?php endforeach ; ?>
  <tr>
    <td><?php echo __("Original and stable id");?></td>
    <td><?php echo   $stable?$stable->getOriginalId():"";?></td>
  </tr>
  <tr>
    <td><?php echo __("UUID");?></td>
    <td><?php echo $stable? $stable->getUuid():"";?></td>
  </tr>
</table>

