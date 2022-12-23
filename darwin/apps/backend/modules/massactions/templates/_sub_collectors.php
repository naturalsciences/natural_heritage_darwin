<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>


<div class="container">
<br/>
<h3 style="color:red;"><?php print(__("Beware : saving an empty widget empty erases all the donators ! Be sure of what your are doing...")); ?></h3>
<table class="full_size" id="people_table_search">
  <thead>
      
	<tbody>
    <?php foreach($form['MassActionForm']['collectors']['Peoples'] as $i=>$form_value):?>
          <?php include_partial('specimensearch/addPeople',array('form' => $form['Peoples'][$i], 'row_line'=>$i));?>
    <?php endforeach;?>
	<tr class="and_row">
        <td colspan="2"></td>
         <td><?php echo image_tag('add_blue.png');?><a href="<?php echo url_for('massactions/addPeople');?>" class="and_people_tag"><?php echo __('Add'); ?></a></td>
    </tr>
  </tbody>
</table>
<table>
<tr>

</table>
<script type="text/javascript">

  
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
  

</script>
</div>
