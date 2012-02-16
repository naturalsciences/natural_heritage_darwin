<table  class="property_values">
  <thead style="<?php echo ($form['RelatedFiles']->count() || $form['newRelatedFiles']->count())?'':'display: none;';?>">
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
	      <?php echo $form['relatedfile'];?>
      </th>
    </tr>
  </thead>
  <tbody id="file_body">
    <?php $retainedKey = 0;?>
    <?php foreach($form['RelatedFiles'] as $form_value):?>
      <?php include_partial('loan/multimedia', array('form' => $form_value, 'row_num'=>$retainedKey, 'edit' => true));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>
    <?php foreach($form['newRelatedFiles'] as $form_value):?>
      <?php include_partial('loan/multimedia', array('form' => $form_value, 'row_num'=>$retainedKey));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>
  </tbody>
  <tfoot>
    <tr>
      <td colspan='5'>
        <ul class="error_list" id="file_error_message" style="display:none">
          <li></li>
        </ul>
        <div class="relatedFile">
          <?php echo $form['filenames']->renderLabel();?>
          <?php echo $form['filenames'];?>
        </div>
          <a href="<?php echo url_for('loan/addRelatedFiles?table='.$table.($form->getObject()->isNew() ? '': '&id='.$form->getObject()->getId()) );?>/num/" id="add_file" class="hidden"></a>
        <iframe name="hiddenFrame" id="hiddenFrame" class="little-frame" style="display:none">
        </iframe>
      </td>
    </tr>
  </tfoot>
</table>
<script  type="text/javascript">
  $(document).ready(function () {
    $('.Add_related_file').change(function()
    {
      hideFileError();
      name = $(this).val().replace(/C:\\fakepath\\/i, '') ;
      form = $(this).closest('form') ;
      form.attr('action','<?php echo url_for("loan/insertFile?table=$table".($form->getObject()->isNew()?"":"&id=".$form->getObject()->getId())) ;?>') ;
      form.attr('target','hiddenFrame') ;
      form.submit();
      hideForRefresh('#refRelatedFiles');        
      return false;
    });
    
    $('#clear_file_error').click(function() { hideFileError(); });
  });
  function getFileInfo(file_id) 
  {
    parent_el = $(this).closest('table.property_values');
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
  }
  function hideFileError()
  {
    $('#file_error_message').hide() ;
  }
</script>
