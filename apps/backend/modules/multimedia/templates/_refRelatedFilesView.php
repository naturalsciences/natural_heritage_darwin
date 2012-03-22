<?php if($files->count() && $atLeastOneFileVisible): ?>
  <table class="catalogue_table_view">
    <thead>
      <tr>
        <th></th>
        <th><?php echo __('Name'); ?></th>
        <th><?php echo __('Description'); ?></th>
        <th><?php echo __('Created At') ; ?></th>
      </tr>
    </thead>
    <tbody id="file_body">
      <?php foreach($files as $file):?>
        <tr>
          <td rowspan="2"><span class="label">&gt;</span></td>
          <td><?php echo $file->getTitle(); ?></td>
          <td><?php echo $file->getDescription(); ?></td>
          <td><?php $date = new DateTime($file->getCreationDate());
                    echo $date->format('d/m/Y'); ?></td>
        </tr>
        <tr>
          <td><?php echo link_to($file->getFileName()." ".image_tag('criteria.png'),'multimedia/downloadFile?id='.$file->getId()) ; ?></td>
          <td colspan="2"><?php echo $file->getMimeType(); ?></td>
        </tr>
      <?php endforeach;?>
    </tbody>
  </table>
<?php endif;?>