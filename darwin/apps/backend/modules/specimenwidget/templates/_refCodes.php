<table  class="property_values">
  <thead style="<?php echo ($form['Codes']->count() || $form['newCodes']->count())?'':'display: none;';?>">
  <!--ftheeten 2018 08 07-->
   <!-- the two TR below 2015 10 15 input mask-->
	<tr>
		<td colspan="7" >
			<div style="font-weight:bold; text-align:center; vertical-align:middle; padding-bottom:10px;">Enable unicity check:<?php echo $form['unicity_check'];?></div>
		</td>
	</tr>
    <tr class="code_masking">
      <th colspan="7">
        <div id="mask_display" class="mask_display">
          <?php echo $form['code_mask']->renderLabel().$form['code_mask']->render().' '.link_to(image_tag('arrow_refresh.png', array("title" => __("Refresh mask"))), 'specimen/getCodeMask', array("id"=>"code_mask_refresh")); ?>
        </div>
      </th>
    </tr>
    <tr class="code_masking">
      <th colspan="7">
        <div class="mask_display">
          <?php echo $form['code_enable_mask']->renderLabel().$form['code_enable_mask']->render();?>
        </div>
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
		<?php if(strpos($_SERVER['REQUEST_URI'],'/part_id/')):?>
            <?php 
			
				$matches=Array();
				preg_match('/.+\/part_id\/([0-9]+)/',$_SERVER['REQUEST_URI'], $matches);			
				$url_copy = 'specimen/copyCode?id='.$matches[1];
				?>
             <div class="add_code" style="visibility:hidden;"> &nbsp;<a href="<?php echo url_for($url_copy);?>/num/" id="copy_code"><?php echo __('Copy code');?></a></div>
           
		<?php endif;?>
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

    // Initialization of codes content
    var initial_code_object = {};
    $(".code_mrac_input_mask").map(
      function () {
        initial_code_object[this.id] = this.value
      }
    );
    // store that codes content into an html5-data attribute
    $("tr.code_masking").closest( 'table.property_values' ).data( "initial_values", initial_code_object );

    //ftheeten 2018 08 08 (some collections allow duplciate
    var  uncheckDuplicates=function()
    {
        if(typeof $("#specimen_collection_ref").val() !=="undefined")
        {
          //alert($("#specimen_collection_ref").val());
            jQuery.getJSON( "<?php print(url_for("collection/descCollectionJSON")); ?>", {id:$("#specimen_collection_ref").val()})
            .done(function(result)
            {
                var allow_duplicates=result[0].allow_duplicates;
                
                if(allow_duplicates)
                {
                    $("#specimen_unicity_check").prop("checked",false);
                }
            }
            );
        }
    }
    <?php if(strpos($_SERVER['REQUEST_URI'],"duplicate_id")):?>
        uncheckDuplicates();
    <?php endif;?>
    //
    
    $('#add_code').click(function()
    {
        
        //ftheeten 2018 08 08 (some collections allow duplciate
        uncheckDuplicates();
        
        hideForRefresh('#refCodes');
        var parent_el = $(this).closest('table.property_values');
        var num_pos = 0+$(parent_el).find('tbody').length;
        var url = $(this).attr('href')+(num_pos);
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
            // Add a new key into the initial_code_object object
            initial_code_object[$("tbody#code_"+num_pos+" input.code_mrac_input_mask").attr('id')] = $("tbody#code_"+num_pos+" input.code_mrac_input_mask").val();
            // ... and replace the table html5-data attribute with this new entry
            $(parent_el).data("initial_values", initial_code_object);
            if (
              $("thead tr.code_masking input.code_mask").val() !== '' &&
              $("thead tr.code_masking input.enable_mask").attr('checked') === 'checked'
            ) {
              $("tbody#code_"+num_pos+" input.code_mrac_input_mask").inputmask($("thead tr.code_masking input.code_mask").val());
            }
          }
        });
        return false;
    });
	
    $('#copy_code').click(function()
    {
        hideForRefresh('#refCodes');
        parent_el = $(this).closest('table.property_values');
        url = $(this).attr('href')+ (0+$(parent_el).find('tbody').length);
        
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

    $("tr.code_masking input.enable_mask").change(
      function()
      {
        if(
          $(this).attr('checked') === 'checked' &&
          $("thead tr.code_masking input.code_mask").val() !== ''
        )
        {
          // find here a way to tell the inputmask event not to delete the content if it doesn't follow the input mask
          // Bring the value of inputmask as the text contained in the #mask_display field
          // For the moment it seems the isValid function is not well implemented (or well understood ;) ) and
          // We will try the latest version of jquery.inputmask later on to validate the application
          // of the mask
          $("input.code_mrac_input_mask").inputmask($("thead tr.code_masking input.code_mask").val());
        }
        else
        {
          // The mask event is removed for all code fields
          $("input.code_mrac_input_mask").inputmask("remove");
          // Then the code fields are reset to the latest modified value
          $("input.code_mrac_input_mask").each(
            function () {
              parent_table = $(this).closest('table.property_values');
              if (typeof $(parent_table).data("initial_values") != "undefined") {
                var initial_code_object = $(parent_table).data("initial_values");
                if (typeof initial_code_object != "undefined" && ($(this).attr('id') in initial_code_object)) {
                  $(this).val(initial_code_object[$(this).attr('id')]);
                }
              }
            }
          );
        }
      }
    );

    // Trigger the enable_mask change if we change the value of code_mask field
    $("thead tr.code_masking input.code_mask").on("change",
      function () {
        if (
          $("tr.code_masking input.enable_mask").attr('checked') === 'checked'
        ) {
          $("tr.code_masking input.enable_mask").change();
        }
      }
    );

    // Potentially refresh the code mask to get one defined for the collection encoded
    $("a#code_mask_refresh").on(
      "click",
      function(event) {
        event.preventDefault();
        if (
          $("input#specimen_collection_ref").length > 0 &&
          $("input#specimen_collection_ref").val() !== '' &&
          Number.isNaN(parseInt($("input#specimen_collection_ref").val())) !== true
        ) {
          var collection_id = $("input#specimen_collection_ref").val();
          var url = $(this).attr('href')+'/collection_id/'+collection_id;
          $.ajax(
            {
              type: "GET",
              url: url,
              success: function(html)
              {
                $("thead tr.code_masking input.code_mask").val(html);
                $("thead tr.code_masking input.code_mask").change();
              }
            }
          )
        }
        return false;
      }
    );
	
    <?php if(strpos($_SERVER['REQUEST_URI'],'/part_id/')):?>
		<?php if(!strpos($_SERVER['REQUEST_URI'],'/create')):?>
			$('#copy_code').click();
		<?php endif;?>		
		$(".class_unicity_check").prop("checked",false);		
	<?php endif;?>


});
</script>
