<table>
  <tbody>
    <?php if($form['acquisition_category']->hasError() || $form['acquisition_date']->hasError()):?>
      <tr>
        <td colspan="2">
          <?php echo $form['acquisition_category']->renderError(); ?>
          <?php echo $form['acquisition_date']->renderError(); ?>
        <td>
      </tr>
    <?php endif; ?>
    <tr>
      <th>
        <?php echo $form['acquisition_category']->renderLabel(); ?>
      </th>
      <td>
        <?php echo $form['acquisition_category']->render(); ?>
      </td>
    </tr>
    <tr>
      <th>
        <?php echo $form['acquisition_date']->renderLabel() ?>
      </th>
      <td>
        <?php echo $form['acquisition_date']->render() ?>
      </td>
    </tr>
  </tbody>
</table>
<script type="text/javascript">
	//2019 05 08
	GetNagoyaDateAcquisition();
	function GetNagoyaDateAcquisition(){
		var d1 = new Date( $("#specimen_acquisition_date_year").val(),$("#specimen_acquisition_date_month").val()-1,$("#specimen_acquisition_date_day").val());
		var d2 = new Date(2014,9,12);
		var dnull = new Date(1899,10,30);
		
		if(d1 > d2){
		//	if(confirm("Enable Nagoya on this specimen")){
			$('#date_acq').val("ok");
		//	}
		}
		if(d1 < d2){
			$('#date_acq').val("nok");
		}
		if(d1.getTime() === dnull.getTime()){
			$('#date_acq').val("");
		}
	}
	
	$("#specimen_acquisition_date_year").change(function(){
		GetNagoyaDateAcquisition();
		setTimeout(function (){ 
			fillcheckandlabels(1);}		//in _nagoya.php
		,500); 
	});
</script>