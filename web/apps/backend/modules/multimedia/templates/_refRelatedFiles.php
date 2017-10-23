<table class="catalogue_table related_files">
  <thead style="<?php echo ($form['RelatedFiles']->count() || $form['newRelatedFiles']->count())?'':'display: none;';?>">
    <tr>
      <th>
        <?php echo __('Name'); ?>
      </th>
      <th>
        <?php echo __('Description'); ?>
      </th>
      <th>
        <?php echo __('Created At') ; ?>
      </th>
      <th>
        <?php if($table!='loans' && $table!='loan_items'):?>
          <?php echo image_tag('blue_eyel.png', array("title" => __('Visible ?'), "alt" => __('Publicly display this file ?')));?>
        <?php endif;?>
      </th>
      <th>
        <?php if($table!='loans' && $table!='loan_items'):?>
          <?php echo image_tag('book.png', array("title" => __('Publishable?'), "alt" => __('Select this file as a publishable file ?')));?>
        <?php endif;?>
      </th>
      <th>
        <?php echo $form['RelatedFiles_holder'];?>
      </th>
    </tr>
  </thead>
  <tbody id="file_body">
    <?php $retainedKey = 0;?>
    <?php foreach($form['RelatedFiles'] as $f_key => $form_value):?>
      <?php include_partial('multimedia/multimedia', array('form' => $form_value, 'row_num'=>$retainedKey, 'edit' => true, 'table' => $table,'object'=>$form->getEmbeddedForm("RelatedFiles")->getEmbeddedForm($f_key)->getObject()));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>
    <?php foreach($form['newRelatedFiles'] as $form_value):?>
      <?php include_partial('multimedia/multimedia', array('form' => $form_value, 'row_num'=>$retainedKey, 'table' => $table, 'object'=>$form->getEmbeddedForm("newRelatedFiles")->getEmbeddedForm($f_key)->getObject()));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="6">
        <ul class="error_list" id="file_error_message" style="display:none">
          <li></li>
        </ul>
        <div class="relatedFile">
          <a href="<?php echo url_for('multimedia/addRelatedFiles?table='.$table.($form->getObject()->isNew() ? '': '&id='.$form->getObject()->getId()) );?>/num/" id="add_file" class="hidden"></a>
          <div id="relatedFileLabel">
            <?php echo $form['filenames']->renderLabel();?>
          </div>
          <div id="relatedFileInput">
            <?php echo $form['filenames'];?>
          </div>
        </div>
        <iframe name="hiddenFrame" id="hiddenFrame">
        </iframe>
      </td>
    </tr>
  </tfoot>
</table>
<script type="text/javascript">
  $(document).ready(function () {
    $('.Add_related_file').change(function()
    {
      hideFileError();
      name = $(this).val().replace(/C:\\fakepath\\/i, '') ;
      form = $(this).closest('form') ;
      recoverAction = form.attr('action');
      form.attr('action','<?php echo url_for("multimedia/insertFile?table=$table&formname=".$form->getName().($form->getObject()->isNew()?"":"&id=".$form->getObject()->getId())) ;?>') ;
      form.attr('target','hiddenFrame') ;
      form.submit();
      form.attr('action', recoverAction);
      form.removeAttr('target');
      hideForRefresh('#refRelatedFiles');
      $('.Add_related_file').val('');
      return false;
    });
  });

  function getFileInfo(file_id)
  {
    parent_el = $('#add_file').closest('table.related_files');
    $.ajax(
    {
      type: "GET",
      url: $('#add_file').attr('href')+ (0+$('#file_body tr').length)+'/file_id/'+file_id,
      success: function(html)
      {
        $('#file_body').append(html);
        $(parent_el).find('thead:hidden').show();
        showAfterRefresh('#refRelatedFiles');
      }
    });
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
