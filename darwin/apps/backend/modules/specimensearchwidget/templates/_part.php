<table>
<thead>
    <tr>
      <th><?php echo $form['category']->renderLabel();?></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php echo $form['category'];?></td>
    </tr>
  </tbody>
  <thead>
    <tr>
      <th><?php echo $form['part']->renderLabel();?></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php echo $form['part'];?></td>
    </tr>
  </tbody>
  <thead>
    <tr>
      <th><?php echo $form['object_name']->renderLabel();?></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php echo $form['object_name'];?></td>
    </tr>
  </tbody>
</table>
<script  type="text/javascript">


var get_specimen_part=function(p_val)
		{
			$.get("<?php echo url_for('specimen/GetSpecimenPart');?>/item/category/type/"+p_val, function (data) {
				
					$('.specimen_search_filter_part').html(data);
				});
			
		}
	
var init=function()
		{
			var init_cat=$('select[name="specimen_search_filters[category]"]').val();
			get_specimen_part(init_cat);
			
		}
		
$(document).ready(
		function()
		{
			$('select[name="specimen_search_filters[category]"]').change(function() {
			
				var cat=$(this).val();
				get_specimen_part(cat);
				//get_specimen_part($(this).val());
			});
		});
		
		init();
		
</script>
