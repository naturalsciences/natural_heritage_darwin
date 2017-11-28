<table>
  <!--jmherpers 2017 11 22-->
  <tr class="sex_top_bordered">
    <th><B>Total specimen count</B></th>
	<td>.</td>
  </tr>
  <tr id='specimen_count_min'  class="sex_middle_bordered">
	<th><?php echo $form['specimen_count_min']->renderLabel();?></th>
	<td>
	  <?php echo $form['specimen_count_min']->renderError();?>
	  <?php echo $form['specimen_count_min']->render() ?>
	</td>
  </tr>
  <tr id='specimen_count_max'  class="sex_middle_bordered">
	<th><?php echo $form['specimen_count_max']->renderLabel();?></th>
	<td>
	  <?php echo $form['specimen_count_max']->renderError();?>
	  <?php echo $form['specimen_count_max']->render() ?>
	</td>
  </tr>
  <tr class="sex_bottom_bordered">
	<th class="top_aligned"><?php echo $form['accuracy']->renderLabel();?></th>
	<td>
	  <?php echo $form['accuracy']->renderError();?>
	  <?php echo $form['accuracy']->render() ?>
	</td>
  </tr>
  <tr>
	<td></td>
  </tr>
  <!--ftheeten 2016 06 22-->
  <!--jmherpers 2017 11 22-->
  <!--<th></th>!!!!!!!!!!!!!!!!!!!!!!!!!!!!!-->
  <tr class="sex_top_bordered">
    <th><B>Males specimen count</B></th>
	<td>.</td>
  </tr>
  <tr id='specimen_count_min' class="sex_middle_bordered">
	<th><?php echo $form['specimen_count_males_min']->renderLabel();?>
	</th>
	<td>
	  <?php echo $form['specimen_count_males_min']->renderError();?>
	  <?php echo $form['specimen_count_males_min']->render() ?> &#9794;
	</td>
  </tr>
  <tr id='specimen_count_max' class="sex_middle_bordered">
	<th><?php echo $form['specimen_count_males_max']->renderLabel();?></th>
	<td>
	  <?php echo $form['specimen_count_males_max']->renderError();?>
	  <?php echo $form['specimen_count_males_max']->render() ?> &#9794;
	</td>
  </tr>
  <tr class="sex_bottom_bordered">
  <th class="top_aligned"><?php echo $form['accuracy_males']->renderLabel();?></th>
	<td>
	  <?php echo $form['accuracy_males']->renderError();?>
	  <?php echo $form['accuracy_males']->render() ?>
	</td>
  </tr>
  <tr>
	<td></td>
  </tr>
  <!--ftheeten 2016 06 22-->
    <!--jmherpers 2017 11 22-->
  <tr class="sex_top_bordered">
    <th><B>Females specimen count</B></th>
	<td>.</td>
  </tr>
  <tr id='specimen_count_min' class="sex_middle_bordered">
	<th><?php echo $form['specimen_count_females_min']->renderLabel();?></th>
	<td>
	  <?php echo $form['specimen_count_females_min']->renderError();?>
	  <?php echo $form['specimen_count_females_min']->render() ?> &#9792;
	</td>
  </tr>
  <tr id='specimen_count_max' class="sex_middle_bordered">
	<th><?php echo $form['specimen_count_females_max']->renderLabel();?></th>
	<td>
	  <?php echo $form['specimen_count_females_max']->renderError();?>
	  <?php echo $form['specimen_count_females_max']->render() ?> &#9792;
	</td>
  </tr>
   <tr class="sex_bottom_bordered">
  <th class="top_aligned"><?php echo $form['accuracy_females']->renderLabel();?></th>
	<td>
	  <?php echo $form['accuracy_females']->renderError();?>
	  <?php echo $form['accuracy_females']->render() ?>
	</td>
  </tr>
  <tr>
	<td></td>
  </tr>
  <!--ftheeten 2016 06 22-->
    <!--jmherpers 2017 11 22-->
  <tr class="sex_top_bordered">
    <th><B>Juveniles specimen count</B></th>
	<td>.</td>
  </tr>
  <tr id='specimen_count_min' class="sex_middle_bordered">
	<th><?php echo $form['specimen_count_juveniles_min']->renderLabel();?></th>
	<td>
	  <?php echo $form['specimen_count_juveniles_min']->renderError();?>
	  <?php echo $form['specimen_count_juveniles_min']->render() ?> Juv.
	</td>
  </tr>
  <tr id='specimen_count_max' class="sex_middle_bordered">
	<th><?php echo $form['specimen_count_juveniles_max']->renderLabel();?></th>
	<td>
	  <?php echo $form['specimen_count_juveniles_max']->renderError();?>
	  <?php echo $form['specimen_count_juveniles_max']->render() ?> Juv.
	</td>
  </tr>
  <tr class="sex_bottom_bordered">
  <th class="top_aligned"><?php echo $form['accuracy_juveniles']->renderLabel();?></th>
	<td>
	  <?php echo $form['accuracy_juveniles']->renderError();?>
	  <?php echo $form['accuracy_juveniles']->render() ?>
	</td>
  </tr>
</table>
<script type="text/javascript">

  function showHideCount_gen(param) {
    var $acc_fld = $('#specimen_accuracy_'+param+'0');
    var $min_fld = $('#specimen_specimen_count_'+param+'min');
    var $max_fld = $('#specimen_specimen_count_'+param+'max');

    // precise
    if($acc_fld.is(':checked')) {
		 //$("#min_males").text("Count");
      $max_fld.closest('tr').hide();
      $max_fld.val( $min_fld.val());
    }else {
      $max_fld.closest('tr').show();
	 // $("#min_males").text("Min.");
	  if(param=='males_'||param=='females_'||param=='juveniles_')
      {
        $('#specimen_accuracy_1').click();
        showHideCount_gen('');
        syncCounters('_max');
      }
    }
  }
  
  //ftheeten 2016 06 22 to replace old 'showHideCount'
  function showHideCount(){
    showHideCount_gen('')
  }
  
  function showHideCountMales(){
    showHideCount_gen('males_')
    showHideCount();
  }
  
  function showHideCountFemales(){
    showHideCount_gen('females_')
    showHideCount();
  }
  
    function showHideCountJuveniles(){
    showHideCount_gen('juveniles_')
    showHideCount();
  }

//ftheeten 2016 06 22
   function syncCounters(param)
   {
        var sumMin=0;
        if(parseInt($('#specimen_specimen_count_males'+param).val())>0)
        {
            sumMin=sumMin+parseInt($('#specimen_specimen_count_males'+param).val());
        }
        if(parseInt($('#specimen_specimen_count_females'+param).val())>0)
        {
            sumMin=sumMin+parseInt($('#specimen_specimen_count_females'+param).val());
        }
        if(parseInt($('#specimen_specimen_count_juveniles'+param).val())>0)
        {
            sumMin=sumMin+parseInt($('#specimen_specimen_count_juveniles'+param).val());
        }
        if(sumMin>0)
        {
            $('#specimen_specimen_count'+param).val(sumMin);
        }
   }  

    function changeMales()
    {
         syncCounters('_min');
         syncCounters('_max');
         showHideCountMales();
    }
    
    function changeFemales()
    {
         syncCounters('_min');
         syncCounters('_max');
         showHideCountFemales();
    }
    
    function changeJuveniles()
    {
         syncCounters('_min');
         syncCounters('_max');
         showHideCountJuveniles();
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
  //ftheeten 2016 06 22
  
  if(parseInt($('#specimen_specimen_count_males_max').val()) == parseInt($('#specimen_specimen_count_males_min').val()) 
  || (!$('#specimen_specimen_count_males_max').val()&&!$('#specimen_specimen_count_males_min').val())
  ){
 
    $('input#specimen_accuracy_males_0').click();
  }
  else {
   
    $('input#specimen_accuracy_males_1').click();
  }
  
  if(parseInt($('#specimen_specimen_count_females_max').val()) == parseInt($('#specimen_specimen_count_females_min').val()) 
  || (!$('#specimen_specimen_count_females_max').val()&&!$('#specimen_specimen_count_females_min').val())
  ){
    $('input#specimen_accuracy_females_0').click();
  }
  else {
    $('input#specimen_accuracy_females_1').click();
  }


  if(parseInt($('#specimen_specimen_count_juveniles_max').val()) == parseInt($('#specimen_specimen_count_juveniles_min').val()) 
  || (!$('#specimen_specimen_count_juveniles_max').val()&&!$('#specimen_specimen_count_juveniles_min').val())
  ){
    $('input#specimen_accuracy_juveniles_0').click();
  }
  else {
    $('input#specimen_accuracy_juveniles_1').click();
  }
    
  showHideCount();
  //ftheeten 2016 06 22
  showHideCountMales();
  showHideCountFemales();
  showHideCountJuveniles();
  
  $('input#specimen_accuracy_1, input#specimen_accuracy_0').click(showHideCount);
  $('#specimen_specimen_count_min,#specimen_specimen_count_max').change(showHideCount);
  
  //ftheeten 2016 06 22
  $('input#specimen_accuracy_males_1, input#specimen_accuracy_males_0').click(showHideCountMales);
  $('#specimen_specimen_count_males_min,#specimen_specimen_count_males_max').change(changeMales);
  $('input#specimen_accuracy_females_1, input#specimen_accuracy_females_0').click(showHideCountFemales);
  $('#specimen_specimen_count_females_min,#specimen_specimen_count_females_max').change(changeFemales);
    $('input#specimen_accuracy_juveniles_1, input#specimen_accuracy_juveniles_0').click(showHideCountJuveniles);
  $('#specimen_specimen_count_juveniles_min,#specimen_specimen_count_juveniles_max').change(changeJuveniles);
});
</script>