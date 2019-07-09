<?php if(isset($search)):?>
<?php if(count($search)==0):?>

  <div class="warn_message no_record_msg">
    <?php echo __('There are no corresponding records.');?>
    <?php if($form->getValue('only_errors')):?>
      <br /><?php echo __('You may want to include <em>rows without errors</em>');?>
      <script language="javascript">
        $(document).ready(function () {
          $('.no_record_msg em').click(function(){
            $('#staging_filters_only_errors').removeAttr('checked');
            $('#import_filter').submit();
          })
        });
      </script>
  </div>
    <?php endif;?>
<?php else:?>
  <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
  <?php include_partial('global/pager_info', array('form' => $form, 'pagerLayout' => $pagerLayout)); ?>
  <div class="edition">
  <table class="staging_table results_container results">
    <thead>
      <th><?php echo __('Actions');?></th>
      <th><?php echo __('Error(s) found') ; ?></th>
      <th><?php echo __('Status');?></th>
      <th><?php echo __('Linked Info');?></th>
      <th><?php echo __('Codes');?></th>
      <?php foreach($fields as $name=>$title):?>
        <th><?php echo __($title);?></th>
      <?php endforeach;?>
      </thead>
      <tbody>
      <?php foreach($search as $row):?>
        <tr>
          <td>
            <?php if(count($row['status']) != 0 ):?>
              <?php echo link_to(image_tag('edit.png', array("title" => __("Edit"))), 'staging/edit?id='.$row['id']);?>
            <?php endif;?>
            <?php echo link_to(image_tag('remove.png', array("title" => __("Delete"))), 'staging/delete?id='.$row['id'],'class=remove_staging');?>
          </td>
          <td class="<?php echo ($row->getStatus()->export()==''?'fld_ok':'fld_tocomplete') ; ?>"><?php echo ($row->getStatus()->export()==''?'0':count(explode(',',$row->getStatus()->export()))) ; ?></td>
          <td  class="<?php if(count($row['status']) != 0 && $row['status']->export() != ''):?>fld_tocomplete<?php else:?>fld_ok<?php endif;?>">
            <?php if(count($row['status']) != 0 && $row['status']->export() != ''):?>
              <?php echo __('Error');?>
            <?php else:?>
              <?php echo __('No problems detected');?>
            <?php endif;?>
          </td>
          <td><?php echo $row['linked_info'];?></td>
          <td class="<?php echo $row->getStatusFor("code");?>">
            <ul class="codes">
              <?php foreach($row['codes'] as $k=>$v):?>
                <li>
                  <?php echo $v->code_prefix.$v->code_prefix_separator.
                  $v->code.$v->code_suffix.$v->code_suffix_separator;?>
                </li>
              <?php endforeach;?>
            </ul>
          </td>
          <?php foreach($fields as $name=>$title):?>
            <td class="<?php echo $row->getStatusFor($name);?>"><?php echo $row[$name];?></td>
          <?php endforeach;?>
          </tr>
      <?php endforeach;?>
    </tbody>
  </table>
  <br/>
</div>
<!--ftheeten 2018 09 02-->
<div>
	Taxonomic reference : <?php echo $form["taxonomic_metadata_ref"];?>
	
</div>
<!---->

<script language="javascript">
$(document).ready(function () {
  $('.remove_staging').click(function(event)
  {
    event.preventDefault();
    if(confirm('<?php echo addslashes(__('All children will be deleted too, Are you sure ?'));?>'))
    {
      $.ajax({
        url: $(this).attr('href'),
        success: function(html)
        {
          if(html == "ok" )
          {
            $('#import_filter').submit();
          }
        }
      });
   }
  });
  
    //ftheeten 2018 09 02
  $(".taxonomy_metadata_ref").val("<?php print($import->getSpecimenTaxonomyRef());?>");
  
   $(".taxonomy_metadata_ref").change(
		function()
		{
			var url_link=$(".ok_link").attr("href");
			url_link=url_link+"/taxonomy_ref/"+$(this).val();
			$(".ok_link").attr("href", url_link);
            
            var url_link=$(".check_link").attr("href");
			url_link=url_link+"/taxonomy_ref/"+$(this).val();
			$(".check_link").attr("href", url_link);
		}
   );
});
</script>
<?php endif;?>
  <div class="blue_link float_left"><?php echo link_to(__('Back to Import'), 'import/index');?></div>
  <!--ftheeten 2017 08 30-->
   <div class="blue_link float_left"><?php echo link_to(__('Try to create missing peoples'), 'staging/createPeoples?import='.$import->getId() );?></div>
  <div class="blue_link float_left"><?php echo link_to(__('Recheck'), 'staging/recheck?import='.$import->getId().'&full_check=true', array("class"=> "check_link"));?></div>
  <div class="blue_link float_left"><?php echo link_to(__('Import "Ok" lines'), 'staging/markok?import='.$import->getId() , array("class"=> "ok_link"));?></div>&#x09;

<?php //Else not valid form
  else:?>
  <?php echo $form['only_errors']->renderError() ?>
  <?php echo $form['bio_geo']->renderError() ?>
<?php endif;?>

