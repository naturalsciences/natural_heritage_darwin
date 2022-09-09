<table>
	<tbody>
		<?php if($form['gtu_from_date']->hasError() || $form['gtu_to_date']->hasError()):?>
			<tr>
				<td colspan="2">
					<?php echo $form['gtu_from_date']->renderError(); ?>
					<?php echo $form['gtu_to_date']->renderError(); ?>
				<td>
			</tr>
		<?php endif; ?>
		<tr>
			<th>
				<?php echo $form['gtu_from_date']->renderLabel(); ?>
			</th>
			<td>
				<?php echo $form['gtu_from_date']->render(); ?>
			</td>
		</tr>
		<tr>
			<th>
				<?php echo $form['gtu_to_date']->renderLabel() ?>
			</th>
			<td>
				<?php echo $form['gtu_to_date']->render() ?>
			</td>
		</tr>
	</tbody>
	<script type="text/javascript">
		//2019 04 24
		GetNagoyaDateSampling();

		
		$("#specimen_gtu_from_date_year").change(function(){
			GetNagoyaDateSampling();
			setTimeout(function (){ 
				fillcheckandlabels(1);} //in _nagoya.php
			,500); 
		});
		
		$("#specimen_gtu_to_date_year").change(function(){
			GetNagoyaDateSampling();
			setTimeout(function (){ 
				fillcheckandlabels(1);} //in _nagoya.php
			,500); 
		});
	</script>
</table>
