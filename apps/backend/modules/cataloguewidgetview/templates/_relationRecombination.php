<table class="catalogue_table_view">
  <thead>
    <tr>
      <th><?php echo __('Combination of');?></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($relations as $renamed):?>
  <tr>
    <td>
      <a class="link_catalogue" title="<?php echo __('Recombination');?>" href="<?php echo url_for($table.'/view?id='.$renamed['record_id_2']) ?>">
        <?php echo $renamed['ref_item']->getNameWithFormat(ESC_RAW)?>
      </a>
      <?php echo image_tag('info.png',"title=info class=info id=info_recombination");?>
      <div class="tree">
      </div>
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
  </tr>
  <?php endforeach;?>
  </tbody>
</table>
