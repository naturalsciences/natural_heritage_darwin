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
        <?php echo __('Type'); ?>
      </th>
	  <th>
        <?php echo __('Sub-type'); ?>
      </th>
      <th>
        <?php echo __('Created At') ; ?>
      </th>
      <th>
        <?php echo image_tag('blue_eyel.png', array("title" => __('Visible ?'), "alt" => __('Publicly display this file ?')));?>
      </th>
      <th>
        <?php echo image_tag('book.png', array("title" => __('Publishable ?'), "alt" => __('Select this file as a publishable file ?')));?>
      </th>
      <th></th>
    </tr>
  </thead>
  <tbody id="property">
    <?php foreach($files as $file):?>
    <tr>
      <td>
        <a class="link_catalogue" title="<?php echo __('Edit file informations');?>" href="<?php echo url_for('multimedia/add?rid='.$file->getId()); ?>"><?php echo $file->getTitle();?></a>
      </td>
      <td><?php echo $file->getDescription(); ?></td>
      <td>
        <?php  /*If image => preview*/ if(in_array($file->getMimeType() ,array('png' => 'image/png', 'jpg' => 'image/jpeg') ) ):?>
          <a href="<?php echo url_for( 'multimedia/downloadFile?id='.$file->getId());?>"><img src="<?php echo url_for('multimedia/preview?id='.$file->getId());?>" width="100" /></a>
        <?php else:?>
          <?php echo link_to($file->getFileName()." ".image_tag('criteria.png'),'multimedia/downloadFile?id='.$file->getId()) ; ?>
        <?php endif;?>
        (<?php echo $file->getHumanSize();?>)
      </td>
      <td><?php echo $file->getMimeType(); ?></td>
	  <td><?php echo $file->getType(); ?></td>
	  <td><?php echo $file->getSubType(); ?></td>
      <td><?php $date = new DateTime($file->getCreationDate());
                echo $date->format('d/m/Y'); ?></td>
      <td><?php echo image_tag(($file->getVisible())?'checkbox_checked.png':'checkbox_unchecked.png', array("title" => __('Visible ?'), "alt" => __('Publicly display this file ?')));?></td>
      <td><?php echo image_tag(($file->getPublishable())?'checkbox_checked.png':'checkbox_unchecked.png', array("title" => __('Visible ?'), "alt" => __('Publicly display this file ?')));?></td>
      <td class="middle_aligned widget_row_delete">
        <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=multimedia&id='.$file->getId());?>" title="<?php echo __('Delete File') ?>"><?php echo image_tag('remove.png'); ?></a>
      </td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>
<br />
<ul class="error_list" id="file_error_message" style="display:none">
  <li></li>
</ul>
<?php echo form_tag('multimedia/insertFile?table='.$table.'&formname=multimedia&id='.$eid, array('enctype'=>'multipart/form-data'));?>
  <div class="relatedFile">
    <a title="<?php echo __('Add Files');?>" href="<?php echo url_for('multimedia/add?table='.$table.'&id='.$eid);?>" id="add_file" class="link_catalogue hidden"></a>
    <div id="relatedFileLabel">
      <label for="multimedia_filenames"><?php echo __('Add File');?></label>
    </div>
    <div id="relatedFileInput">
      <input class="Add_related_file" type="file" name="multimedia[filenames]" id="multimedia_filenames">
    </div>
  </div>
  <iframe name="hiddenFrame" id="hiddenFrame">
  </iframe>
</form>
<script type="text/javascript">
  $(document).ready(function () {
    $('.Add_related_file').change(function()
    {
      hideFileError();
      name = $(this).val().replace(/C:\\fakepath\\/i, '') ;
      form = $(this).closest('form') ;
      form.attr('target','hiddenFrame') ;
      form.submit();
      form.removeAttr('target');
      hideForRefresh('#refRelatedFiles');
      return false;
    });
  });

  function getFileInfo(file_id)
  {
    recoverHref = $('#add_file').attr('href');
    $('#add_file').attr('href', $('#add_file').attr('href')+'/file_id/'+file_id);
    $('#add_file').trigger('click');
    $('#add_file').attr('href', recoverHref);
  }

  function displayFileError(err_msg)
  {
    $('#file_error_message li:first').html(err_msg) ;
    $('#file_error_message').show() ;
    showAfterRefresh('#refRelatedFiles');
  }

  function hideFileError()
  {
    $('#file_error_message').hide() ;
  }
</script>
