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
        var url_nagoya=location + "/../getNagoyaCollection";
		
	$(document).ready(
		function()
		{
			console.log("check nago acqu0");
			GetNagoyaDateAcquisition();
		}
		);
	
	$(".group_date_specimen_acquisition_date").change(function(){
		console.log("check nago acqu1");
        GetNagoyaCollection(url_nagoya);
		console.log("check nago acqu2");
		GetNagoyaDateAcquisition();
		setTimeout(function (){ 
			console.log("check nago acqu3");
			fillcheckandlabels(1);}		//in _nagoya.php
		,500); 
	});
</script>