<?php if($files->count() && $atLeastOneFileVisible): ?>
  <table class="catalogue_table_view">
    <thead>
      <tr>
        <th><?php echo __('Name'); ?></th>
        <th><?php echo __('Description'); ?></th>
        <th><?php echo __('Created At') ; ?></th>
      </tr>
    </thead>
    <tbody id="file_body">
      <?php $row_num = 0;?>
      <?php foreach($files as $file):?>
        <?php $row_num+=1;?>
        <tr class="row_num_<?php echo $row_num;?>">
          <td><?php echo $file->getTitle(); ?></td>
          <td><?php echo $file->getDescription(); ?></td>
          <td><?php $date = new DateTime($file->getCreationDate());
                    echo $date->format('d/m/Y'); ?></td>
        </tr>
        <tr class="row_num_<?php echo $row_num;?>">
          <td>
            <?php $alt=($file->getDescription()!='')?$file->getTitle().' / '.$file->getDescription():$file->getTitle();?>
            <?php if($file->hasPreview()):?>
              <a href="<?php echo url_for('multimedia/downloadFile?id='.$file->getId());?>" alt="<?php echo $alt;?>" title="<?php echo $alt;?>"><img src="<?php echo url_for('multimedia/preview?id='.$file->getId());?>" alt="<?php echo $alt;?>" width="100" /></a>
            <?php else:?>
              <?php echo link_to($file->getFileName()." ".image_tag('criteria.png'),'multimedia/downloadFile?id='.$file->getId(), array('alt'=>$alt, 'title'=>$alt)) ; ?>
            <?php endif;?>
          </td>
          <td colspan="2"><?php echo $file->getMimeType(); ?></td>
        </tr>
        <script type="text/javascript">
          $("tr.row_num_<?php echo $row_num;?>").hover(function(){
                                                                  parent_el = $(this).closest('tbody');
                                                                  parent_tr = $(parent_el).children('tr.row_num_<?php echo $row_num;?>');
                                                                  $(parent_tr).css('background-color', '#E9EDBE');
                                                                },
                                                      function(){
                                                                  parent_el = $(this).closest('tbody');
                                                                  parent_tr = $(parent_el).children('tr.row_num_<?php echo $row_num;?>');
                                                                  $(parent_tr).css('background-color', '#F6F6F6');
                                                                });
        </script>
      <?php endforeach;?>
    </tbody>
  </table>
<?php endif;?>
