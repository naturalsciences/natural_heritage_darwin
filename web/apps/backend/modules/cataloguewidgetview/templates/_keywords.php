<table class="catalogue_table_view">
  <thead>
    <tr>
      <th><?php echo __('Keyword');?></th>
      <th><?php echo __('Value');?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($keywords as $keyword):?>
    <tr>
      <td>
        <?php echo $keyword->getReadableKeywordType();?>
      </td>
      <td>
        <?php echo $keyword->getKeyword();?>
      </td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>
