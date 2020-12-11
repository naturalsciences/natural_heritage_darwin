<?php if($files->count() && $atLeastOneFileVisible): ?>
  <table class="catalogue_table_view" >
    <thead>
      <tr>
        <th style="word-wrap: break-word; max-width: 1px;"><?php echo __('Name'); ?></th>
        <th style="word-wrap: break-word; max-width: 1px;"><?php echo __('Description'); ?></th>
        <th style="word-wrap: break-word; max-width: 1px;"><?php echo __('Type'); ?></th>
        <th style="word-wrap: break-word; max-width: 1px;"><?php echo __('Created At') ; ?></th>
        <th style="word-wrap: break-word; max-width: 1px;"><?php echo __('Technical Parameters') ; ?></th>
        <th style="word-wrap: break-word; max-width: 1px;"><?php echo __('Field Observations') ; ?></th>
      </tr>
    </thead>
    <tbody id="file_body">
      <?php $row_num = 0;?>
      <?php foreach($files as $file):?>
        <?php $row_num+=1;?>
        <tr class="row_num_<?php echo $row_num;?>" >
          <td style="word-wrap: break-word; max-width: 1px;" ><?php echo $file->getTitle(); ?></td>
          <td style="word-wrap: break-word; max-width: 1px;"><?php echo $file->getDescription(); ?></td>
          <td style="word-wrap: break-word; max-width: 1px;"><?php echo $file->getType(); ?></td>
          <td style="word-wrap: break-word; max-width: 1px;"><?php $date = new DateTime($file->getCreationDate());
                    echo $date->format('d/m/Y H:M:S'); ?></td>
          <td style="word-wrap: break-word; max-width: 1px;"><?php echo $file->getTechnicalParameters(); ?></td>
          <td style="word-wrap: break-word; max-width: 1px;"><?php echo $file->getFieldObservations(); ?></td>
        </tr>
        <tr class="row_num_<?php echo $row_num;?>">
          <td style="word-wrap: break-word; max-width: 1px;">
            <?php $alt=($file->getDescription()!='')?$file->getTitle().' / '.$file->getDescription():$file->getTitle();?>
            <?php if($file->hasPreview()):?>
              <a href="<?php echo url_for('multimedia/downloadFile?id='.$file->getId());?>" alt="<?php echo $alt;?>" title="<?php echo $alt;?>"><img src="<?php echo url_for('multimedia/preview?id='.$file->getId());?>" alt="<?php echo $alt;?>" width="100" /></a>
            <?php else:?>
              <?php echo link_to($file->getFileName()." ".image_tag('criteria.png'),'multimedia/downloadFile?id='.$file->getId(), array('alt'=>$alt, 'title'=>$alt)) ; ?>
            <?php endif;?>
            <?php if($file->getExternalUri()):?>
                 <a href="<?php echo $file->getExternalUri();?>" alt="<?php echo $alt;?>" title="<?php echo $alt;?>" target="_blank"><?php echo $file->getExternalUri();?></a>
            <?php endif;?>
          </td>
          <td colspan="4" style="word-wrap: break-word; max-width: 1px;"><?php echo $file->getMimeType(); ?></td>
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
