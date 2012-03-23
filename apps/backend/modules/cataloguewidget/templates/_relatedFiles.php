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
      <td>
        <a class="link_catalogue" title="<?php echo __('Edit file informations');?>" href="<?php echo url_for('multimedia/add?rid='.$file->getId()); ?>"><?php echo $file->getTitle();?></a>
      </td>
      <td><?php echo $file->getDescription(); ?></td>
      <td><?php echo link_to($file->getFileName()." ".image_tag('criteria.png'),'multimedia/downloadFile?id='.$file->getId()) ; ?></td>
      <td colspan="2"><?php echo $file->getMimeType(); ?></td>
      <td><?php $date = new DateTime($file->getCreationDate());
                echo $date->format('d/m/Y'); ?></td>
      <td><?php echo $file->getVisible();?></td>
      <td><?php echo $file->getPublishable();?></td>
      <td class="widget_row_delete">
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
<?php echo form_tag('multimedia/add?table='.$table.'&id='.$eid, array('enctype'=>'multipart/form-data'));?>
  <div class="relatedFile">
    <a href="<?php echo url_for('multimedia/addRelatedFiles?table='.$table.'&id='.$eid );?>/num/" id="add_file" class="hidden"></a>
    <label for="multimedia_filenames"><?php echo __('Add Files');?></label><input class="Add_related_file" type="file" name="multimedia[filenames]" id="multimedia_filenames">
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
      recoverAction = form.attr('action');
      form.attr('action','<?php echo url_for("multimedia/insertFile?table=".$table."&formname=multimedia&id=".$eid) ;?>') ;
      form.attr('target','hiddenFrame') ;
      form.submit();
      form.attr('action', recoverAction);
      form.removeAttr('target');
      hideForRefresh('#refRelatedFiles');
      return false;
    });
  });

  function getFileInfo(file_id)
  {
    alert(file_id);
//     parent_el = $('#add_file').closest('table.property_values');
//     $.ajax(
//     {
//       type: "GET",
//       url: $('#add_file').attr('href')+ (0+$('#file_body tr').length)+'/file_id/'+file_id,
//       success: function(html)
//       {
//         $('#file_body').append(html);
//         $(parent_el).find('thead:hidden').show();
//         showAfterRefresh('#refRelatedFiles');
//       }
//     });
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
