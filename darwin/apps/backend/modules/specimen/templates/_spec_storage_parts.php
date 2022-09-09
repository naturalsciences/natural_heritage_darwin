  <tbody  class="spec_storage_parts_data" id="spec_storage_parts_data_<?php echo $rownum;?>">
   <tr class="spec_storage_parts_data">
   
      <td>
        <table>
            <tr>
                <td>
                    <?php echo $form['category']->renderLabel();?>
               </td>
                <td>
                    <?php echo $form->renderError();?>

                    <?php echo $form['category']->renderError(); ?>
                    <?php echo $form['category'];?>
              </td>
           </tr>
           <tr>
                <td>
                    <?php echo $form['specimen_status']->renderLabel();?>
               </td>
                <td>
                    <?php echo $form->renderError();?>

                    <?php echo $form['specimen_status']->renderError(); ?>
                    <?php echo $form['specimen_status'];?>
              </td>
           </tr>
           <tr>
                <td>
                    <?php echo $form['complete']->renderLabel();?>
               </td>
                <td>
                    <?php echo $form->renderError();?>

                    <?php echo $form['complete']->renderError(); ?>
                    <?php echo $form['complete'];?>
              </td>
           </tr>
           <tr>
                <td>
                    <?php echo $form['institution_ref']->renderLabel();?>
               </td>
              <td>
                <?php echo $form['institution_ref']->renderError(); ?>
                <?php echo $form['institution_ref'];?>
                <?php echo $form['check'];?>
              </td>
            </tr>
            <tr>
              <td>
                    <?php echo $form['specimen_part']->renderLabel();?>
               </td>
              <td>
                <?php echo $form['specimen_part']->renderError(); ?>
                <?php echo $form['specimen_part'];?>
                
              </td>
            </tr>
            <tr>
              <td>
                    <?php echo $form['object_name']->renderLabel();?>
               </td>
              <td>
                <?php echo $form['object_name']->renderError(); ?>
                <?php echo $form['object_name'];?>
                
              </td>
            </tr>
            <tr>
              <td>
                    <?php echo $form['building']->renderLabel();?>
               </td>
              <td>
                <?php echo $form['building']->renderError(); ?>
                <?php echo $form['building'];?>
                
              </td>
            </tr>
            <tr>
              <td>
                    <?php echo $form['floor']->renderLabel();?>
               </td>
              <td>
                <?php echo $form['floor']->renderError(); ?>
                <?php echo $form['floor'];?>
                
              </td>
            </tr>
            <tr>
              <td>
                    <?php echo $form['room']->renderLabel();?>
               </td>
              <td>
                <?php echo $form['room']->renderError(); ?>
                <?php echo $form['room'];?>
                
              </td>
            </tr>
            <tr>
                <td>
                    <?php echo $form['row']->renderLabel();?>
               </td>
              <td>
                <?php echo $form['row']->renderError(); ?>
                <?php echo $form['row'];?>
                
              </td>
            </tr>
            <tr>
                <td>
                    <?php echo $form['col']->renderLabel();?>
               </td>
              <td>
                <?php echo $form['col']->renderError(); ?>
                <?php echo $form['col'];?>
                
              </td>
            </tr>
            <tr>
                <td>
                    <?php echo $form['shelf']->renderLabel();?>
               </td>
              <td>
                <?php echo $form['shelf']->renderError(); ?>
                <?php echo $form['shelf'];?>
                
              </td>
            </tr>
             <tr>
                <td>
                    <?php echo $form['surnumerary']->renderLabel();?>
               </td>
              <td>
                <?php echo $form['surnumerary']->renderError(); ?>
                <?php echo $form['surnumerary'];?>
                
              </td>
            </tr>
             <tr>
                <td>
                    <?php echo $form['container']->renderLabel();?>
               </td>
              <td>
                <?php echo $form['container']->renderError(); ?>
                <?php echo $form['container'];?>
                
              </td>
            </tr>
            <tr>
                <td>
                    <?php echo $form['container_type']->renderLabel();?>
               </td>
              <td>
                <?php echo $form['container_type']->renderError(); ?>
                <?php echo $form['container_type'];?>
                
              </td>
            </tr>
            <tr>
                <td>
                    <?php echo $form['container_storage']->renderLabel();?>
               </td>
              <td>
                <?php echo $form['container_storage']->renderError(); ?>
                <?php echo $form['container_storage'];?>
                
              </td>
            </tr>
            <tr>
                <td>
                    <?php echo $form['sub_container']->renderLabel();?>
               </td>
              <td>
                <?php echo $form['sub_container']->renderError(); ?>
                <?php echo $form['sub_container'];?>
                
              </td>
            </tr>
            <tr>
                <td>
                    <?php echo $form['sub_container_type']->renderLabel();?>
               </td>
              <td>
                <?php echo $form['sub_container_type']->renderError(); ?>
                <?php echo $form['sub_container_type'];?>
                
              </td>
            </tr>
            <tr>
                <td>
                    <?php echo $form['sub_container_storage']->renderLabel();?>
               </td>
              <td>
                <?php echo $form['sub_container_storage']->renderError(); ?>
                <?php echo $form['sub_container_storage'];?>
                
              </td>
            </tr>
            <tr>
            <tr>
              <td>
                <?php echo $form['check'];?>
              </td>
            </tr>
         </table>
      </td>
      <?php if($rownum>0):?>
          <td class="widget_row_delete">
            <?php echo image_tag('remove.png', 'alt=Delete class=clear_storage_parts id=clear_storage_parts_'.$rownum); ?>
            <?php echo $form->renderHiddenFields() ?>
          </td>
       <?php endif;?>      
    </tr>
   <tr>
     <td colspan="3"><hr /></td>
   </tr>
  </tbody>
  <script type="text/javascript">
    $(document).ready(function () {
      $("#clear_storage_parts_<?php echo $rownum;?>").click( function()
      {
      
        parent_el = $(this).closest('tbody');
        parentTableId = $(parent_el).closest('table').attr('id');
       
        $(parent_el).find('textarea[id$=\"_category\"]').val(''); 
        
        $(parent_el).find('input[id$=\"_check\"]').remove();        
        $(parent_el).hide();
        visibles = $('table#'+parentTableId+' tbody.spec_storage_parts_data:visible').size();
        if(!visibles)
        {
          $(this).closest('table#'+parentTableId).find('thead').hide();
        }
      });
      
      $('select[name$="[container_type]"]').change(function() {
      parent_el = $(this).closest('.widget');
      $.get("<?php echo url_for('specimen/getStorage');?>/item/container/type/"+$(this).val(), function (data) {
              parent_el.find('select[name$="[container_storage]"]').html(data);
            });
    });

    $('select[name$="[sub_container_type]"]').change(function() {
      parent_el = $(this).closest('.widget');
      $.get("<?php echo url_for('specimen/getStorage');?>/item/sub_container/type/"+$(this).val(), function (data) {
             parent_el.find('select[name$="[sub_container_storage]"]').html(data);
            });
    });
    //this part ftheeten 2017 01 13


	  	 var url="<?php echo(url_for('catalogue/storageAutocomplete?'));?>";

	  $('.autocomplete_for_building').autocomplete({
		source: function (request, response) {
        
			$.getJSON(url, {
						term : request.term,
                        entry : 'building',
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
    
    	  $('.autocomplete_for_status').autocomplete({
		source: function (request, response) {
        
			$.getJSON(url, {
						term : request.term,
                        entry : 'specimen_status',
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
    
      $('.autocomplete_for_floor').autocomplete({
		source: function (request, response) {
        
			$.getJSON(url, {
						term : request.term,
                        entry : 'floor',
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
    
      $('.autocomplete_for_room').autocomplete({
		source: function (request, response) {
        
			$.getJSON(url, {
						term : request.term,
                        entry : 'room',
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
    
          $('.autocomplete_for_col').autocomplete({
		source: function (request, response) {
        
			$.getJSON(url, {
						term : request.term,
                        entry : 'col',
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
    
     $('.autocomplete_for_row').autocomplete({
		source: function (request, response) {
        
			$.getJSON(url, {
						term : request.term,
                        entry : 'row',
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
    
     $('.autocomplete_for_shelf').autocomplete({
		source: function (request, response) {
        
			$.getJSON(url, {
						term : request.term,
                        entry : 'shelf',
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
    
    //ftheeten 2018 02 09
        $('.ui-autocomplete').mouseleave(
        function()
        {
            $(this).hide();
            
        }
    );
    
    });
  </script>
