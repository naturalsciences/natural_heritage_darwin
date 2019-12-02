<table>
<tr>
<td>    
  <?php echo $form['MassActionForm']['add_gtu_tag']['group_name']->renderError(); ?>
 <?php echo $form['MassActionForm']['add_gtu_tag']['group_name']; ?>
<input type="button" value="Add Group" id="add_group">
  </div>
  <div id="target_sub_group">
  </div>
    

 <div class="sub_group">
 
 

    <?php echo $form['MassActionForm']['add_gtu_tag']['sub_group_name']->renderError(); ?>
    <?php echo $form['MassActionForm']['add_gtu_tag']['sub_group_name'];?>
  </div>

  <div class="tag_encod">

    <?php echo $form['MassActionForm']['add_gtu_tag']['tag_value']->renderError(); ?>
    <?php echo $form['MassActionForm']['add_gtu_tag']['tag_value'];?>

    <div class="purposed_tags">
    </div>
  </div>
</td>
</tr>
</table>

 
  


<script  type="text/javascript">




$(document).ready(function () {

  $('#add_group').click(function()
    {
      
        var select=$("select.mass_tags_sub_group");
        select.find('option').remove();
        var selected_group = $('select.mass_tags_group option:selected').val();
        $.getJSON("<?php print(url_for("gtu/getTagSubGroup")); ?>", {tag:selected_group}, function(result) 
        {
            
            $.each(result, function(item) {
                if(item.length>0)
                {
                    select.append($('<option>', { 
                        value: item,
                        text : item 
                        }));
                }
            });
            
        });
    });

 
 changeSubmit(true);
  });
</script>

