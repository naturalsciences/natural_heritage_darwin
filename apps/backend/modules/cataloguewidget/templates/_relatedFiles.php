<table class="catalogue_table">
  <thead>
    <tr>
      <th>
        <?php echo __('Name'); ?>
      </th>
      <th>
        <?php echo __('Description'); ?>
      </th>
      <th>
        <?php echo __('File'); ?>
      </th>
      <th>
        <?php echo __('Format'); ?>
      </th>
      <th>
        <?php echo __('Created At') ; ?>
      </th>
      <th>
        <?php echo image_tag('blue_eyel.png', array("title" => __('Visible ?'), "alt" => __('Publicly display this file ?')));?>
      </th>
      <th>
        <?php echo image_tag('book.png', array("title" => __('Publishable?'), "alt" => __('Select this file as a publishable file ?')));?>
      </th>
      <th></th>
    </tr>
  </thead>
  <tbody id="property">
    <?php foreach($files as $file):?>
    <tr>
      <td><?php echo $file->getTitle(); ?></td>
      <td><?php echo $file->getDescription(); ?></td>
      <td><?php echo link_to($file->getFileName()." ".image_tag('criteria.png'),'multimedia/downloadFile?id='.$file->getId()) ; ?></td>
      <td colspan="2"><?php echo $file->getMimeType(); ?></td>
      <td><?php $date = new DateTime($file->getCreationDate());
                echo $date->format('d/m/Y'); ?></td>
      <td><?php echo $file->getVisible();?></td>
      <td><?php echo $file->getPublishable();?></td>
      <td class="widget_row_delete">
        <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=multimedia&id='.$file->getId());?>" title="<?php echo __('Delete File') ?>"><?php echo image_tag('remove.png'); ?>
        </a>
      </td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>
<br />
<?php echo image_tag('add_green.png');?><a title="<?php echo __('Add Files');?>" class="link_catalogue" href="<?php echo url_for('multimedia/add?table='.$table.'&id='.$eid); ?>"><?php echo __('Add');?></a>
