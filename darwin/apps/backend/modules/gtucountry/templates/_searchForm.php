<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<div class="catalogue_gtucountry">
<?php echo form_tag('gtucountry/search'.( isset($is_choose) && $is_choose  ? '?is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'gtucountry_filter'));?>
  <div class="container">
	<div class="" style="float:left;" >
    <table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
      <thead> 
			<tr>
				<th><?php echo $form['iso3166']->renderLabel() ?></th>
				<th><?php echo $form['name']->renderLabel(); ?></th>
				<th><?php echo $form['historical_name']->renderLabel(); ?></th>
			</tr>
	  </thead>
	  <tbody>
			<tr>
				<td><?php echo $form['iso3166']; ?></td>
				<td><?php echo $form['name']; ?></td>
				<td><?php echo $form['historical_name']; ?></td>
			</tr>
	  </tbody>
	</table>	  
  </div>
 
      <div class="edit" style="float:left;" >
        <input   class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" />
	</div>
	<div class="search_results">
        <div class="search_results_content"></div>
      </div> 
</div>
</form>
		
</div>

<script type="text/javascript">
 $(document).ready(function () 
	{
		 $('.catalogue_gtucountry').choose_form({});
	});
 
</script>
