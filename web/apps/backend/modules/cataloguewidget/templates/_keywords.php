<table class="catalogue_table">
  <thead>
    <tr>
      <th><?php echo __('Keyword');?></th>
      <th><?php echo __('Value');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($keywords as $keyword):?>
    <tr>
      <td>
      <a class="link_catalogue" title="<?php echo __('Edit Keywords');?>"  href="<?php echo url_for('catalogue/keyword?table='.$table.'&id='.$eid.'&kingdom='.$kingdom); ?>">
        <?php echo $keyword->getReadableKeywordType();?>
      </a>
      </td>
      <td>
        <?php echo $keyword->getKeyword();?>
      </td>
      <td class="widget_row_delete">
        <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=classification_keywords&id='.$keyword->getId());?>" title="<?php echo __('Delete Keywords') ?>"><?php echo image_tag('remove.png'); ?>
        </a>
      </td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>
<br />
<?php echo image_tag('add_green.png');?><a title="<?php echo __('Add Keywords');?>" class="link_catalogue" href="<?php echo url_for('catalogue/keyword?table='.$table.'&id='.$eid.'&kingdom='.$kingdom); ?>"><?php echo __('Add');?></a>
