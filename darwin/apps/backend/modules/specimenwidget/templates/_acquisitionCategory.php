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
	<script type="text/javascript">
		//2019 05 08
		GetNagoyaDateAcquisition();

		
		$("#specimen_acquisition_date_year").change(function(){
			GetNagoyaDateAcquisition();
			/*setTimeout(function (){ 
				fillcheckandlabels(1);}		//in _nagoya.php
			,500);*/ 
		});
	</script>
</table>
