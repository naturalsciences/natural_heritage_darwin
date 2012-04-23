<table class="catalogue_table">
  <thead>
    <tr>
      <th><?php echo __('Combination of');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($relations as $renamed):?>
  <tr>
    <td>
      <a class="link_catalogue" title="<?php echo __('Recombination');?>" href="<?php echo url_for('catalogue/relation?type=recombined&table='.$table.'&rid='.$eid.'&id='.$renamed['id']) ?>"><?php echo $renamed['ref_item']->getNameWithFormat(ESC_RAW)?></a>
      <?php echo image_tag('info-green.png',"title=info class=info id=info_recombination");?>
      <p class="tree">
      </p>
      <script type="text/javascript">
       $('#info_recombination').click(function() 
       {   
         item_row = $(this).closest('td') ;
         if(item_row.find('.tree').is(":hidden"))
         {
           $.get('<?php echo url_for('catalogue/tree?table='.$table.'&id='.$renamed['record_id_2']) ; ?>',function (html){
             item_row.find('.tree').html(html).slideDown();
             });
         }
         item_row.find('.tree').slideUp();
       });
      </script>       
    </td>
    <td class="widget_row_delete">   
      <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=catalogue_relationships&id='.$renamed['id']);?>" title="<?php echo __('Delete Recombination') ?>"><?php echo image_tag('remove.png'); ?>
      </a>
    </td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>
<br />
<?php if(count($relations) <= 1 ):?><?php echo image_tag('add_green.png');?><a title="<?php echo __('Recombination');?>" class="link_catalogue" href="<?php echo url_for('catalogue/relation?type=recombined&table='.$table.'&rid='.$eid);?>"><?php else:?><?php echo image_tag('add_grey.png');?><span class='add_not_allowed'><?php endif;?><?php echo __('Add');?><?php if(count($relations) <= 1 ):?></a><?php else:?></span><?php endif;?>
