<table class="catalogue_table_view">
  <thead>
    <tr>
      <th><?php echo __('Type');?></th>
      <th><?php echo __('Items');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($synonyms as $group_name => $group):?>
    <tr>
      <td>
        <?php $groups=Doctrine::getTable('ClassificationSynonymies')->findGroupnames() ; echo $groups[$group_name];?>
      </td>
      <td>
        <table class="grp_id_<?php echo $group[0]['group_id'];?> widget_sub_table" alt="<?php echo $group_name;?>">
          <thead>
            <tr>
        <th><?php echo __('Name');?></th>
        <th>
          <?php if($group_name == 'rename'):?>
            <?php echo __('Current');?>
          <?php elseif($group_name != "homonym"):?>
            <?php echo __('Basionym');?>
          <?php endif;?>
        </th>
        <th></th>
            </tr>
          </thead>
          <tbody >
          <?php foreach($group as $synonym):?>
            <tr class="syn_id_<?php echo $synonym['id'];?>" id="id_<?php echo $synonym['id'];?>">
        <td>
          <?php if($synonym['record_id'] == $eid):?>
              <strong><?php echo $synonym['ref_item']->getNameWithFormat(ESC_RAW);?></strong>
          <?php else:?>
            <a class="link_catalogue" title="<?php echo __('Synonym');?>" href="<?php echo url_for($table.'/view?id='.$synonym['record_id']) ?>">
              <?php echo $synonym['ref_item']->getNameWithFormat(ESC_RAW);?>
            </a>
          <?php endif;?>
          <?php echo image_tag('info.png',"title=info class=info id=info_".$synonym['id']);?>
          <div class="tree">
          </div>
          <script type="text/javascript">
          $('#info_<?php echo $synonym['id'];?>').click(function()
          {
            item_row = $(this).closest('td') ;
            if(item_row.find('.tree').is(":hidden"))
            {
              $.get('<?php echo url_for('catalogue/tree?table='.$table.'&id='.$synonym['record_id']) ; ?>',function (html){
                item_row.find('.tree').html(html).slideDown();
                });
            }
            item_row.find('.tree').slideUp();
          });
          </script>
        </td>
        <td class="basio_cell">
          <?php if($group_name != "homonym"):?>
            <?php if($synonym['is_basionym']) echo image_tag('checkbox_checked.png') ; else echo image_tag('checkbox_unchecked.png') ;?>
          <?php endif;?>
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
