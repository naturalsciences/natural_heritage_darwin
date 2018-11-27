<div class="container">
<table class="full_size" id="people_table_search">
  <thead>
      <tr>
      <td colspan="3">
		Boolean Selector:<?php echo $form['people_boolean'];?></td>
     </td>
	</tr>
	<tbody>
    <?php foreach($form['Peoples'] as $i=>$form_value):?>
          <?php include_partial('specimensearch/addPeople',array('form' => $form['Peoples'][$i], 'row_line'=>$i));?>
    <?php endforeach;?>
	<tr class="and_row">
        <td colspan="2"></td>
         <td><?php echo image_tag('add_blue.png');?><a href="<?php echo url_for('specimensearch/addPeople');?>" class="and_people_tag"><?php echo __('Add'); ?></a></td>
    </tr>
  </tbody>
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
