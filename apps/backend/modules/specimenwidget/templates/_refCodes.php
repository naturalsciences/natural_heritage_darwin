<table  class="property_values">
  <thead style="<?php echo ($form['Codes']->count() || $form['newCodes']->count())?'':'display: none;';?>">
    <tr class="code_masking">
      <th colspan="7">
        <div id="mask_display" class="mask_display"><?php echo $form['code_mask']->renderRow(); ?></div>
      </th>
    </tr>
    <tr class="code_masking">
      <th colspan="7">
        <div class="mask_display"><?php echo $form['code_enable_mask']->renderRow();?></div>
      </th>
      </tr>
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
      <?php include_partial('specimen/spec_codes', array('form' => $form_value, 'rownum'=>$retainedKey, "codemask"=>$form->getObject()->getCollections()->getCodeMask()));?>
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
          <?php
          if($module == 'specimen') $url = 'specimen/addCode';
          if($module == 'loan_items') $url = 'loanitem/addCode';
          ?>
          <a href="<?php echo url_for($url. ($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>/num/" id="add_code"><?php echo __('Add Code');?></a>
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
        <?php
          $object_arr = $form->getObject()->toArray();
          if(!empty($object_arr['collection_ref'])):
        ?>
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
    
    $("select#<?php echo $module;?>_prefix_separator").change(function()
    {
      parent_el = $(this).closest('table');
      $(parent_el).find('tbody select[id$=\"_prefix_separator\"]').val($(this).val());
    });

    $("select#<?php echo $module;?>_suffix_separator").change(function()
    {
      parent_el = $(this).closest('table');
      $(parent_el).find('tbody select[id$=\"_suffix_separator\"]').val($(this).val());
    });

    $(".enable_mask").change(
      function()
      {
        if( $("tr.code_masking input.enable_mask").attr('checked') === 'checked' )
        {
          // find here a way to tell the inputmask event not to delete the content if it doesn't follow the input mask
          // Bring the value of inputmask as the text contained in the #mask_display field
          // For the moment it seems the isValid function is not well implemented (or well understood ;) ) and
          // We will try the latest version of jquery.inputmask later on to validate the application
          // of
          $(".code_mrac_input_mask").inputmask($("thead tr.code_masking input.code_mask").val());
        }
        else
        {
          $(".code_mrac_input_mask").inputmask('remove');
          $(".code_mrac_input_mask").each(
            function(index) {
              console.log(index+': '+$(this).attr('value'));
            }
          );
        }
      }
    );

});
</script>
