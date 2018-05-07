<table>
    <tr>
      <th ><?php echo $form['specimen_status']->renderLabel();?></th>
      <td><?php echo $form['specimen_status'];?></td>
    </tr>
    <tr>
      <th class="top_aligned"><?php echo $form['specimen_part']->renderLabel();?></th>
      <td><?php echo $form['specimen_part'];?></td>
    </tr>
   <tr>
      <th class="top_aligned"><?php echo $form['object_name']->renderLabel();?></th>
      <td><?php echo $form['object_name'];?></td>
    </tr>
  <tr>
    <th><?php echo $form['institution_ref']->renderLabel();?></th>
    <td><?php echo $form['institution_ref']->render() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo $form['building']->renderLabel();?></th>
	<td><?php echo $form['building']->render() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo $form['floor']->renderLabel();?></th>
	<td><?php echo $form['floor']->render() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo $form['room']->renderLabel();?></th>
	<td><?php echo $form['room']->render() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo $form['row']->renderLabel();?></th>
	<td><?php echo $form['row']->render() ?></td>
  </tr>
    <tr>
  <th class="top_aligned"><?php echo $form['col']->renderLabel('Column');?></th>
  <td><?php echo $form['col']->render() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo $form['shelf']->renderLabel();?></th>
	<td><?php echo $form['shelf']->render() ?></td>
  </tr>
  <tr>
	<th><?php echo $form['container']->renderLabel();?></th>
	<td><?php echo $form['container']->render() ?></td>
  </tr>


<!--pvignaux20160606-->
  <tr>
	<th><?php echo $form['container_type']->renderLabel();?></th>
	<td><?php echo $form['container_type']->render() ?></td>
  </tr>


<!--pvignaux20160606-->
  <tr>
	<th><?php echo $form['container_storage']->renderLabel();?></th>
	<td><?php echo $form['container_storage']->render() ?></td>
  </tr>


  <tr>
	<th><?php echo $form['sub_container']->renderLabel();?></th>
	<td><?php echo $form['sub_container']->render() ?></td>
  </tr>


<!--pvignaux20160606-->
  <tr>
	<th><?php echo $form['sub_container_type']->renderLabel();?></th>
	<td><?php echo $form['sub_container_type']->render() ?></td>
  </tr>


<!--pvignaux20160606-->
  <tr>
	<th><?php echo $form['sub_container_storage']->renderLabel();?></th>
	<td><?php echo $form['sub_container_storage']->render() ?></td>
  </tr>


</table>
<script  type="text/javascript">

var autocomplete_rmca_array=Array();


$(document).ready(function () {

//JMHerpers 2018 04 16
 $('#specimen_search_filters_sub_container_type').css( "maxWidth", 400);
  
  //ftheeten 2015 06 08
  //autocomplete for codes number
  
  $('.col_check').change(
		function(i)
		{
				autocomplete_rmca_array=$('.col_check:checked').map(function(){
				return $(this).val();
				}).get();
			
		}
  );
	 var url="<?php echo(url_for('catalogue/storageAutocomplete?'));?>";

	  $('.autocomplete_for_building').autocomplete({
		source: function (request, response) {
			$.getJSON(url, {
						term : request.term,
                        entry : 'building',
						collections: autocomplete_rmca_array.join()
					} , 
					function (data) 
						{
					response($.map(data, function (value, key) {
					return value;
                    }));
			});
		},
		minLength: 1,
		delay: 100
	});
    

    
    $('.autocomplete_for_parts').autocomplete({
		source: function (request, response) {
			$.getJSON(url, {
						term : request.term,
                        entry : 'specimen_part',
						collections: autocomplete_rmca_array.join()
					} , 
					function (data) 
						{
					response($.map(data, function (value, key) {
					return value;
                    }));
			});
		},
		minLength: 1,
		delay: 100
	});
    
    $('.autocomplete_for_status').autocomplete({
		source: function (request, response) {
			$.getJSON(url, {
						term : request.term,
                        entry : 'specimen_status',
						collections: autocomplete_rmca_array.join()
					} , 
					function (data) 
						{
					response($.map(data, function (value, key) {
					return value;
                    }));
			});
		},
		minLength: 1,
		delay: 100
	});
    
        $('.autocomplete_for_floor').autocomplete({
		source: function (request, response) {
			$.getJSON(url, {
						term : request.term,
                        entry : 'floor',
						collections: autocomplete_rmca_array.join()
					} , 
					function (data) 
						{
					response($.map(data, function (value, key) {
					return value;
                    }));
			});
		},
		minLength: 1,
		delay: 100
	});
    
    $('.autocomplete_for_row').autocomplete({
		source: function (request, response) {
			$.getJSON(url, {
						term : request.term,
                        entry : 'row',
						collections: autocomplete_rmca_array.join()
					} , 
					function (data) 
						{
					response($.map(data, function (value, key) {
					return value;
                    }));
			});
		},
		minLength: 1,
		delay: 100
	});
    
     $('.autocomplete_for_col').autocomplete({
		source: function (request, response) {
			$.getJSON(url, {
						term : request.term,
                        entry : 'col',
						collections: autocomplete_rmca_array.join()
					} , 
					function (data) 
						{
					response($.map(data, function (value, key) {
					return value;
                    }));
			});
		},
		minLength: 1,
		delay: 100
	});
    
     $('.autocomplete_for_room').autocomplete({
		source: function (request, response) {
			$.getJSON(url, {
						term : request.term,
                        entry : 'room',
						collections: autocomplete_rmca_array.join()
					} , 
					function (data) 
						{
					response($.map(data, function (value, key) {
					return value;
                    }));
			});
		},
		minLength: 1,
		delay: 100
	});
    
     $('.autocomplete_for_shelf').autocomplete({
		source: function (request, response) {
			$.getJSON(url, {
						term : request.term,
                        entry : 'shelf',
						collections: autocomplete_rmca_array.join()
					} , 
					function (data) 
						{
					response($.map(data, function (value, key) {
					return value;
                    }));
			});
		},
		minLength: 1,
		delay: 100
	});
    
        //ftheeten 2018 02 09
        $('.ui-autocomplete').mouseleave(
        function()
        {
            $(this).hide();
            
        }
    );

});
</script>