<div id="taxon_orig" class="hidden warn_message"><?php echo __('The taxon you chose, was marked as "renamed".');?>
<br />
<?php echo __('Click on the name below to replace the unit by its current name.');?>
<span></span>
</div>

<?php echo $form['taxon_ref']->renderError() ?>
<?php echo $form['taxon_ref']->render() ?>

<script  type="text/javascript">
  $('#specimen_taxon_ref').bind('change',function()
  {
    
    $.getJSON('<?php echo url_for('catalogue/getCurrent?table=taxonomy');?>/id/'+$('#specimen_taxon_ref').val(), function(data) {
      $('#taxon_orig span').text(data.name).attr('r_id', data.id);
      $('#taxon_orig').removeClass('hidden');
    });
  });
  $('#taxon_orig span').click(function()
  {
    $('#specimen_taxon_ref_name').text($('#taxon_orig span').text());
    $('#specimen_taxon_ref').val($('#taxon_orig span').attr('r_id'));
    $('#taxon_orig').fadeOut();
  }
  );

</script>
