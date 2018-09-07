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

  function showHideCount() {
    var $acc_fld = $('#specimen_accuracy_0');
    var $min_fld = $('#specimen_specimen_count_min');
    var $max_fld = $('#specimen_specimen_count_max');

    // precise
    if($acc_fld.is(':checked')) {
      $max_fld.closest('tr').hide();
      $max_fld.val( $min_fld.val());
    }else {
      $max_fld.closest('tr').show();
    }
  }

$(document).ready(function()
{
  // Init to not imprecise
  if(parseInt($('#specimen_specimen_count_max').val()) == parseInt($('#specimen_specimen_count_min').val()) ){
    $('input#specimen_accuracy_0').click();
  }
  else {
    $('input#specimen_accuracy_1').click();
  }

  showHideCount();

  $('input#specimen_accuracy_1, input#specimen_accuracy_0').click(showHideCount);
  $('#specimen_specimen_count_min,#specimen_specimen_count_max').change(showHideCount);
});
</script>
