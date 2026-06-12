<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php echo javascript_include_tag('catalogue_people.js') ?>
	<table>
	<thead>
	<tr >
		<th>
			
		</th>	
	<th>
      <?php echo $form['MassActionForm']['add_identification']['notion_date']->renderLabel();?>
    </th>
    <th>
      <?php echo $form['MassActionForm']['add_identification']['notion_concerned']->renderLabel();?>
    </th>
    <th>
      <?php echo $form['MassActionForm']['add_identification']['value_defined']->renderLabel();?>
    </th>
    <th>
      <?php echo $form['MassActionForm']['add_identification']['determination_status']->renderLabel();?>
    </th>		
	</tr>
	</thead>
<tbody class="spec_ident_data" id="spec_ident_data_0"> 
	
	<tr class="spec_ident_data">
    <td >
      
    </td>
    <td>
      <?php echo $form['MassActionForm']['add_identification']['notion_date'];?>
    </td>
    <td>
      <?php echo $form['MassActionForm']['add_identification']['notion_concerned'];?>
    </td>
    <td>
      <?php echo $form['MassActionForm']['add_identification']['value_defined'];?>
    </td>
    <td>
      <?php echo $form['MassActionForm']['add_identification']['determination_status'];?>
    </td>
    
  </tr>
  <tr>
  <th></th>
   <th><?php echo $form['MassActionForm']['add_identification']['use_taxon_as_value']->renderLabel();?></th>
  </tr>
  <tr>
  <td></td>
  <td><?php echo $form['MassActionForm']['add_identification']['use_taxon_as_value'];?></td>
  </tr>
  
  </table>
  <tr class="spec_ident_identifiers">
    <td></td>
	<th colspan="4"><b><?php print(__("Identifier"));?></b></th>
  </tr>
  <tr class="spec_ident_identifiers">
    <td></td>
    <td colspan="4">
		<table class="full_size" id="people_table_search">
		  <thead>
		  </thead>    
			<tbody>
			<?php foreach($form['MassActionForm']['add_identification']['Peoples'] as $i=>$form_value):?>
				  <?php include_partial('specimensearch/addPeople',array('form' => $form['MassActionForm']['add_identification']['Peoples'][$i], 'row_line'=>$i));?>
			<?php endforeach;?>
			<tr class="and_row">
				<td colspan="2"></td>
				 <td><?php echo image_tag('add_blue.png');?><a href="<?php echo url_for('massactions/addIdentifier');?>" class="and_people_tag"><?php echo __('Add'); ?></a></td>
			</tr>
		  </tbody>
		</table>
    </td>
	</tr>
	
	 <tr>
	 <table>
	 <tr>
	  <th></th>
	   <th style="text-align:right;"><?php echo $form['MassActionForm']['add_identification']['behaviour']->renderLabel();?></th>
	  </tr>
	  <tr>
	  <td></td>
	  <td style="text-align:right;"><?php echo $form['MassActionForm']['add_identification']['behaviour'];?></td>
		</tr>
	</table>
  </tr>
  
	<tr>
    <td>
<script  type="text/javascript">
  $(document).ready(function () {
   

	var num_fld = 1;
      $('.and_people_tag').click(function()
      {
        
		hideForRefresh('#people_role');
		$.ajax({
          type: "GET",
          url: $(this).attr('href') + '/num/' + (num_fld++) ,
          success: function(html)
          {
            $('table#people_table_search > tbody .and_row').before(html);
            showAfterRefresh('#people_role');
          }
        });
        return false;
      });  

      changeSubmit(true);
});
</script></td>
  </tr>
</tbody>
