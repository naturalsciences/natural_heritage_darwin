<table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['specimen_part']['specimen_part']->renderLabel();?>
      </th>
      <td>
        <?php echo $form['MassActionForm']['specimen_part']['specimen_part']->renderError();?>
        <?php echo $form['MassActionForm']['specimen_part']['specimen_part'];?>
      </td>
    </tr>
  </table>

  <script  type="text/javascript">
	 var url="<?php echo(url_for('catalogue/storageAutocomplete?'));?>";
  
  $(document).ready(function () {
  
  
		$('.autocomplete_for_parts').autocomplete({
			source: function (request, response) {        
			$.getJSON(url, {
						term : request.term,
                        entry : 'specimen_part',
						//collections: $('.col_check').val(),
                       
                        timeout: 3000
					} , 
					function (data) 
						{
					response($.map(data, function (value, key) {
					return value;
                    }));
			});
		},
		minLength: 1,
		delay: 1000
	});
      changeSubmit(true);
  });
  </script>