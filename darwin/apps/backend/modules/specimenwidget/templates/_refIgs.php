<?php echo $form['ig_ref']->renderError() ?>
<?php echo $form['ig_ref']->render() ?>

<!--<div class="add_code">-->
<!--<?php echo link_to(__('Check and create I.G.'),'igs/new', array("target"=>"_blank"));?>-->
<!--</div>-->
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
