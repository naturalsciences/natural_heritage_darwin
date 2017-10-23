<table class="catalogue_table">
  <thead>
    <tr>
      <th></th>
      <th><?php echo __('Relation');?></th>
      <th></th>      
      <th></th>
      <th><?php echo __('Role');?></th>
      <th class="datesNum"><?php echo __('Period');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($relations as $relation):?>
    <tr class="rid_<?php echo $relation->Parent->getId();?>">
    <td>  
      <a class="link_catalogue" title="<?php echo __('Edit Relation');?>"  href="<?php echo url_for('people/relation?ref_id='.$eid.'&id='.$relation->getId());?>">
	<?php echo $relation->Child;?>
      </a>
    </td>
    <td><?php echo $relation->getRelationshipType();?></td>
    <td><?php echo image_tag('info.png',"title=info class='info lid_1' id='info_".$relation->Parent->getId()."'");?><?php echo $relation->Parent;?>
      <div class="tree">
      </div>
    </td>
    <td><?php echo $relation->getPersonUserRole();?></td>
    <td class="datesNum">
	<?php echo $relation->getActivityDateFromObject()->getDateMasked('em','Y',ESC_RAW);?> - 
	<?php echo $relation->getActivityDateToObject()->getDateMasked('em','Y', ESC_RAW) ?>
    </td>    
    <td class="widget_row_delete">     
      <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=people_relationships&id='.$relation->getId());?>" title="<?php echo __('Are you sure ?') ?>">
	<?php echo image_tag('remove.png'); ?>
      </a>
    </td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>

<br />
<?php echo image_tag('add_green.png');?>
<a title="<?php echo __('Add Relationship');?>" class="link_catalogue" href="<?php echo url_for('people/relation?ref_id='.$eid);?>"><?php echo __('Add');?></a>
<script language="javascript">
  function fetchRelDetails()
  { 
    item_row=$(this).closest('tr');
    el_id  = getIdInClasses(item_row);
    level = getIdInClasses(this);
    if($(item_row).find('.tree').is(":hidden"))
    {	    
      $.get('<?php echo url_for('people/relationDetails');?>/id/'+el_id+'/level/'+level,function (html){
        if(html=='nothing')
        {
          item_row.find('.info').attr('src','<?php echo url_for('/images/info-bw.png');?>');
        }
        else
        {  
             $(item_row).find('.tree').html(html).slideDown();

        }
      });
    }
    else
      $(item_row).find('.tree').slideUp();   
  }
  $(".info").live('click',fetchRelDetails);
</script>
