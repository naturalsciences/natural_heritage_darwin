<table>
  <tbody>
    <?php if($form['host_relationship']->hasError()):?>
      <tr>
        <td colspan="2"><?php echo $form['host_relationship']->renderError(); ?><td>
      </tr>
    <?php endif; ?>
    <tr>
      <th>
        <?php echo $form['host_relationship']->renderLabel() ?>
      </th>
      <td>
        <?php echo $form['host_relationship']->render() ?>
      </td>
    </tr>
    <?php if($form['host_taxon_ref']->hasError()):?>
      <tr>
        <td colspan="2"><?php echo $form['host_taxon_ref']->renderError(); ?><td>
      </tr>
    <?php endif; ?>
    <tr>
      <th>
        <?php echo $form['host_taxon_ref']->renderLabel() ?>
      </th>
      <td>
        <?php echo $form['host_taxon_ref']->render() ?>
      </td>
    </tr>
    <?php if($form['host_specimen_ref']->hasError()):?>
      <tr>
        <td colspan="2"><?php echo $form['host_specimen_ref']->renderError(); ?><td>
      </tr>
    <?php endif; ?>
    <tr id="host_specimen_ref">
      <th>
        <?php echo $form['host_specimen_ref']->renderLabel(); ?>
      </th>
      <td>       
        <?php echo $form['host_specimen_ref']->render(); ?>
      </td>
    </tr>
  </tbody>
</table>
<?php $hostTaxonId = $form['host_taxon_ref']->renderId(); $hostSpecimenId = $form['host_specimen_ref']->renderId(); ?>
<script type="text/javascript">
$(document).ready(function ()
{
  function clearVals(fieldCleared)
  {
    $('#'+fieldCleared).val('');
    $('#'+fieldCleared+'_name').text('-');
    $('#'+fieldCleared+'_button').find('.but_text').text('<?php echo addslashes(__('Choose !'));?>');
    $('#'+fieldCleared+'_clear').hide();
  }

  $('tr#host_specimen_ref input[type="hidden"]').bind('loadref',function()
    {
      ref_caller_id = '<?php echo $form->getObject()->getId() ; ?>';
      if (ref_caller_id.length)
      {
        ref_caller_id = '/caller_id/'+ref_caller_id;
      }

      button = $(this).parent().find('.but_text');

      if(button.data('href') == null)
      {
        button.data('href', button.attr('href'));
      }
      
      button.attr('href', button.data('href') + ref_caller_id);
    });


  $('#<?php echo $hostTaxonId; ?>_clear').click(function()
    {
      clearVals('<?php echo $hostSpecimenId; ?>');   
    });
  
  $('#<?php echo $hostSpecimenId; ?>_clear').click(function()
    {
      if($('#<?php echo $hostTaxonId; ?>').val() != '')
      {
        if(confirm('<?php echo addslashes(__('Clear also host taxon reference ?')); ?>'))
        {
          clearVals('<?php echo $hostTaxonId; ?>');
        }
      }
    });

  $('#<?php echo $hostTaxonId; ?>').change(function()
    {
      if($(this).val() != '' && $('#<?php echo $hostSpecimenId; ?>').val() != '')
      {
        $.ajax(
          {
            url: '<?php echo url_for("specimen/sameTaxon");?>'+'?specId='+$('#<?php echo $hostSpecimenId; ?>').val()+'&taxonId='+$(this).val(),
            success: function(html) {
                       if(html != "ok" )
                       {
                         clearVals('<?php echo $hostSpecimenId; ?>');
                       }
                     }
          });
      }
    });

  $('#<?php echo $hostSpecimenId; ?>').change(function()
    {

      if($(this).val() != '')
      {      
        $.ajax(
          {
            url: '<?php echo url_for('specimen/getTaxon');?>'+'?specId='+$(this).val()+'&targetField=<?php echo $hostTaxonId; ?>',
            dataType: 'json',
            success: function(html) 
                     {
                       $('#<?php echo $hostTaxonId; ?>').val(html.<?php echo $hostTaxonId; ?>);
                       $('#<?php echo $hostTaxonId; ?>_name').html('<i>' + html.<?php echo $hostTaxonId.'_name'; ?> + '</i>');
                       if($('#<?php echo $hostTaxonId; ?>_name').text() == '-')
                       {
                         $('#<?php echo $hostTaxonId; ?>_clear').hide();
                         $('#<?php echo $hostTaxonId; ?>_button').find('.but_text').text('<?php echo addslashes(__('Choose !')); ?>');
                       }
                       else
                       {
                         $('#<?php echo $hostTaxonId; ?>_clear').show();
                         $('#<?php echo $hostTaxonId; ?>_button').find('.but_text').text('<?php echo addslashes(__('Change !')); ?>');
                       }
                     }
          });
      }
    });

});
</script>
