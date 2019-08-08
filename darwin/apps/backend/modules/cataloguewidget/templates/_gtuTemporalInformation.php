 <div id="table_gtu_date">
  <table>
    <tbody>
      <tr>
        <th class="top_aligned"><?php echo __("Dates") ?></th>
        <td>
         <div style="display:None"><?php echo $form['delete_mode'] ?></div>
          <?php echo $form['temporal_information']->renderError() ?>
          <?php echo $form['temporal_information'] ?>          
        </td>
        <td>
            <div id="counter_date" class="counter_date">Value(s)</div>
        </td>
      </tr>
      <tr>
        <td><input type="button" class='remove_date' id='remove_date' value="Remove date"</input></td>
      </tr>
      <tr>
        <td colspan="3">            
            <div><?php echo $form['new_from_date']->renderLabel(); ?></div>
			<div><?php echo $form['new_from_date']->renderError(); ?></div>
            <div><?php echo $form['new_from_date']; ?></div>
            <div><?php echo $form['new_to_date']->renderLabel(); ?></div>
            <div><?php echo $form['new_to_date']->renderError(); ?></div>
            <div><?php echo $form['new_to_date']; ?></div>
            <input class='add_date' id='add_date_2' value="Add date" type="button" />
        </td>
      </tr>
    </tbody>
</table>
</div>
<script  type="text/javascript">
    

$(document).ready(function () {


    $('.counter_date').text($('#gtu_temporal_information option').size()+" Value(s)");
    $('.add_date').live('click', function(event)
        {
			$tmpform= $("form:first"); 
			$('#table_gtu_date :input').each(function() {

				$(this).appendTo($tmpform);
			});			
            $("#submit").click();
           event.preventDefault();
        }
    );
    
     $('.remove_date').live('click', function(event)
        {
            
             
            $('#gtu_delete_mode').prop('checked', true); 
			$tmpform= $("form:first"); 
			$('#table_gtu_date :input').each(function() {

				$(this).appendTo($tmpform);
			});			
            //document.forms[0].submit();
			 $("#submit").click();
            event.preventDefault();
        }
    );
	
	 $('#add_date_2').live('click', function(event)
        {
           
            //document.forms[0].submit();
           
        }
    );
}); 

		

	
</script>