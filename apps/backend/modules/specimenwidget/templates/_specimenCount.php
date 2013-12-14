<table>
  <tr>
	<th class="top_aligned"><?php echo $form['accuracy']->renderLabel();?></th>
	<td>
	  <?php echo $form['accuracy']->renderError();?>
	  <?php echo $form['accuracy']->render() ?>
	</td>
  </tr>
  <tr id='specimen_count_min'>
	<th><?php echo $form['specimen_count_min']->renderLabel();?></th>
	<td>
	  <?php echo $form['specimen_count_min']->renderError();?>
	  <?php echo $form['specimen_count_min']->render() ?>
	</td>
  </tr>
  <tr id='specimen_count_max'>
	<th><?php echo $form['specimen_count_max']->renderLabel();?></th>
	<td>
	  <?php echo $form['specimen_count_max']->renderError();?>
	  <?php echo $form['specimen_count_max']->render() ?>
	</td>
  </tr>
</table>
<script type="text/javascript">
$(document).ready(function()
{
  if($('input#specimen_accuracy_0:checked').length || ( $('#specimen_specimen_count_max').val() == $('#specimen_specimen_count_min').val()) )
  {
    $('input#specimen_accuracy_0').click();
    $('tr#specimen_count_max').hide();
  }
  $("input[name=\"specimen\\[accuracy\\]\"]").click(function ()
  {
    if($('input#specimen_accuracy_0:checked').length)
    {
        $('tr#specimen_count_max').hide();
    }
    else
    {
        $('tr#specimen_count_max').show();
    }
    if(parseInt($('#specimen_specimen_count_max').val()) < parseInt($('#specimen_specimen_count_min').val()) )
        $('#specimen_specimen_count_max').val($('#specimen_specimen_count_min').val());
  });
});
</script>