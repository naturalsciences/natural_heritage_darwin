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
        <?php echo __('Updated on') ; ?>
      </th>
      <th>
	      <?php echo $form['relatedfile'];?>
      </th>
    </tr>
  </thead>
  <tbody id="file_body">
    <?php $retainedKey = 0;?>
    <?php foreach($form['RelatedFiles'] as $form_value):?>
      <?php include_partial('loan/multimedia', array('form' => $form_value, 'row_num'=>$retainedKey, 'new' => true));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>
    <?php foreach($form['newRelatedFiles'] as $form_value):?>
      <?php include_partial('loan/multimedia', array('form' => $form_value, 'row_num'=>$retainedKey, 'new' => false));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>
  </tbody>
  <tfoot>
    <tr>
      <td colspan='5'><?php echo $form['filenames'];?>
        <div class="add_code">
          <a href="<?php echo url_for('loan/addRelatedFiles'. ($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>/num/" id="add_file" class="hidden"></a>
        </div>
        <iframe name="hiddenFrame" class="little-frame">
        </iframe>
      </td>
    </tr>
  </tfoot>
</table>
<script  type="text/javascript">
  $(document).ready(function () {
    $('.Add_related_file').change(function()
    {
      name = $(this).val().replace(/C:\\fakepath\\/i, '') ;
      form = $(this).closest('form') ;
      form.attr('action','<?php echo url_for("loan/insertFile?table=loans".(isset($eid)?"&id=".$eid:'')) ;?>') ;
      form.attr('target','hiddenFrame') ;
      form.submit();
      hideForRefresh('#refRelatedFiles');
      parent_el = $(this).closest('table.property_values');
      $.ajax(
      {
        type: "GET",
        url: $('#add_file').attr('href')+ (0+$('#file_body').find('tr').length)+'/title/'+name,
        success: function(html)
        {
          $('#file_body').append(html);
          $(parent_el).find('thead:hidden').show();
          showAfterRefresh('#refRelatedFiles');
        }
      });
      return false;
    });
  });
</script>
