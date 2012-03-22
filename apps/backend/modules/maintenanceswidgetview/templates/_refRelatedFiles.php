<?php if(count($files) > 0) : ?>
<table class="catalogue_table_view">
  <thead>
    <tr>
      <th><?php echo __('Name'); ?></th>
      <th><?php echo __('Description'); ?></th>
      <th><?php echo __('File'); ?></th>
      <th><?php echo __('Format'); ?></th>
      <th><?php echo __('Created At') ; ?></th>
    </tr>
  </thead>
  <tbody id="file_body">
    <?php foreach($files as $file):?>
    <tr>
      <td><?php echo $file->getTitle(); ?></td>
      <td><?php echo $file->getDescription(); ?></td>
      <td><?php echo link_to($file->getFileName()." ".image_tag('criteria.png'),'multimedia/downloadFile?id='.$file->getId()) ; ?></td>
      <td><?php echo $file->getMimeType(); ?></td>
      <td> <?php $date = new DateTime($file->getCreationDate());
                echo $date->format('d/m/Y'); ?></td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>
<?php endif ; ?>
