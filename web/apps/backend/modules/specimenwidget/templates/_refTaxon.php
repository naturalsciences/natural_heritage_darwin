<div id="taxon_orig" class="hidden warn_message"><?php echo __('The taxon you chose, was marked as "renamed".');?>
<br />
<?php echo __('Click on the name below to replace the unit by its current name.');?>
<span></span>
</div>

<?php echo $form['taxon_ref']->renderError() ?>
<?php echo $form['taxon_ref']->render() ?>

<script  type="text/javascript">
  function loadCurrent() {
    if($('#specimen_taxon_ref').val() != '') {
      // Fetch the current name of the taxa
      $.getJSON('<?php echo url_for('catalogue/getCurrent?table=taxonomy');?>/id/' + $('#specimen_taxon_ref').val(), function(data) {
        if(data.id) {
          $('#taxon_orig span').text(data.name).attr('r_id', data.id);
          $('#taxon_orig').removeClass('hidden');
        }
      });
    }
  }
  loadCurrent();
  $('#specimen_taxon_ref').bind('change',loadCurrent);
  $('#taxon_orig span').click(function()
  {
    $('#specimen_taxon_ref_name').val($('#taxon_orig span').text());
    $('#specimen_taxon_ref').val($('#taxon_orig span').attr('r_id'));
    $('#taxon_orig').addClass('hidden');
  }
  );

</script>
