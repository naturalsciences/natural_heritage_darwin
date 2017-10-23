<table class="catalogue_table">
  <thead>
    <tr>
      <th><?php echo __('Type');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($types as $type => $items):?>
    <tr>
      <td class="data_grouping">
        <?php echo __(ucfirst($type)); ?>
      </td>
      <td>
        <table class="widget_sub_table" alt="<?php echo $type;?>">
          <thead>
            <tr>
              <th></th>
              <th><?php echo __('People');?></th>
              <th><?php echo __('Sub-Type');?></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($items as $person):?>
            <tr class="peo_id_<?php echo $person->getId();?>" id="id_<?php echo $person->getId();?>">
              <td class="handle"><?php echo image_tag('drag.png');?></td>
              <td>
                  <a class="link_catalogue" title="<?php echo __('Edit People');?>" href="<?php echo url_for('cataloguepeople/people?table='.$table.'&rid='.$eid.'&id='.$person->getId()); ?>">
                    <?php echo $person->People->getFormatedName();?>
                  </a>	  
              </td>
              <td class="catalogue_people_sub_type">
                <?php echo __($person->getPeopleSubType());?>
              </td>
              <td class="widget_row_delete">	
                <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=catalogue_people&id='.$person->getId());?>" title="<?php echo __('Delete People') ?>">
                  <?php echo image_tag('remove.png'); ?>
                </a>  
              </td>
            </tr>
          <?php endforeach;?>
          </tbody>
        </table>
      </td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>
<br />
<?php echo image_tag('add_green.png');?><a title="<?php echo __('Add People');?>" class="link_catalogue" href="<?php echo url_for('cataloguepeople/people?table='.$table.'&rid='.$eid); ?>"><?php echo __('Add');?></a>
<script type="text/javascript">

function forceHelper(e,ui)
{
   $(".ui-state-highlight").html("<td colspan='3'>&nbsp;</td>");
}

$(document).ready(function()
{

  $("#cataloguePeople .widget_sub_table tbody").sortable({
      placeholder: 'ui-state-highlight',
      handle: '.handle',
      axis: 'y',
      change: function(e, ui) {
	forceHelper(e,ui);
      },
      deactivate: function(event, ui) {
	  el_Array = $(this).sortable('toArray');
	  result='';
    for(i=0;i<el_Array.length;i++)
	  {
	    result += getIdInClasses( $('#'+el_Array[i]) )+',';
	  }
	  $.ajax({
	    type: "POST",
	    url: "<?php echo url_for('cataloguepeople/editOrder?table='.$table.'&rid='.$eid); ?>",
	    data: { order: result, people_type: $(this).parent().attr('alt') }
	  });
      }
    });
});
</script>
