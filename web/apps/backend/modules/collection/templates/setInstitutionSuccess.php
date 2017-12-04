<?php echo $form['institution_ref']->renderError() ?>
<?php echo $form['institution_ref'] ?>
<script> 
  $("#collections_institution_ref").change(function() {
    $.get("<?php echo url_for('collection/completeOptions');?>/institution/"+$(this).val(), function (data) {
	    $("#collections_parent_ref").html(data);
    });
  });
</script>
