<table  class="property_values">
  <thead style="<?php echo ($form['Codes']->count() || $form['newCodes']->count())?'':'display: none;';?>">
    <tr>
      <th>
        <?php echo __('Category'); ?>
      </th>
      <th>
        <?php echo __('Prefix'); ?>
      </th>
      <th>
        <?php echo __('sep.'); ?>
      </th>
      <th>
        <?php echo __('Code'); ?>
      </th>
      <th>
        <?php echo __('sep.'); ?>
      </th>
      <th>
        <?php echo __('Suffix'); ?>
      </th>
      <th>
      </th>
    </tr>
    <tr>
      <th colspan='2'>
        <?php echo $form['Codes_holder'];?>
      </th>
      <th class="reseted">
        <?php echo $form['prefix_separator'];?>
      </th>
      <th>
      </th>
      <th class="reseted">
        <?php echo $form['suffix_separator'];?>
      </th>
      <th colspan='3'>      
      </th>
    </tr>
  </thead>
    <?php $retainedKey = 0;?>
    <?php foreach($form['Codes'] as $form_value):?>
      <?php include_partial('specimen/spec_codes', array('form' => $form_value, 'rownum'=>$retainedKey));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>
    <?php foreach($form['newCodes'] as $form_value):?>
      <?php include_partial('specimen/spec_codes', array('form' => $form_value, 'rownum'=>$retainedKey));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>
  <tfoot>
    <tr>
      <td colspan='8'>
        <div class="add_code">
          <?php if($module == 'specimen') $url = 'specimen/addCode';
          if($module == 'parts') $url = 'parts/addCode';?>
          <a href="<?php echo url_for($url. ($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>/num/" id="add_code"><?php echo __('Add code');?></a>
        </div>
      </td>
    </tr>
  </tfoot>

</table>
<script  type="text/javascript">
$(document).ready(function () {

    $('#add_code').click(function()
    {
        hideForRefresh('#refCodes');
        parent_el = $(this).closest('table.property_values');
        url = $(this).attr('href')+ (0+$(parent_el).find('tbody').length);
        <?php if($module == 'specimen'):?>
          url += '/collection_id/' + $('input#specimen_collection_ref').val();
        <?php endif;?>
        $.ajax(
        {
          type: "GET",
          url: url,
          success: function(html)
          {
            $(parent_el).append(html);
            $(parent_el).find('thead:hidden').show();
            showAfterRefresh('#refCodes');
          }
        });
        return false;
    });
    
    $('select#specimen_prefix_separator').change(function()
    {
      parent_el = $(this).closest('table');
      $(parent_el).find('tbody select[id$=\"_prefix_separator\"]').val($(this).val());
    });

    $('select#specimen_suffix_separator').change(function()
    {
      parent_el = $(this).closest('table');
      $(parent_el).find('tbody select[id$=\"_suffix_separator\"]').val($(this).val());
    });

});
</script>
