<?php echo $form['ig_ref']->renderError() ?>
<?php echo $form['ig_ref']->render() ?>

<script type="text/javascript">
$('#specimen_ig_ref_check').change(function(){
  if($(this).val()) 
  {
    $.ajax({
      type: 'POST',
      url: "<?php echo url_for('igs/addNew') ?>",
      data: "num="+$('#specimen_ig_ref_name').val(),
      success: function(html){
        $('li#toggledMsg').hide();
        $('#specimen_ig_ref').val(html) ;
      }
    });  
  }
}) ;
</script>
