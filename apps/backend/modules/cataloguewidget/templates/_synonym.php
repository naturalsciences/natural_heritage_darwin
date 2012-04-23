<table class="catalogue_table">
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
        <table class="grp_id_<?php echo $group[0]['group_id'];?> widget_sub_table" alt="<?php echo __($group_name);?>">
          <thead>
            <tr>
        <th></th>
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
              <td class="handle"><?php echo image_tag('drag.png');?></td>
              <td>
                <?php if($synonym['record_id'] == $eid):?>
                  <strong><?php echo $synonym['ref_item']->getNameWithFormat(ESC_RAW);?></strong>
                <?php else:?>
                  <a title="<?php echo __('Synonym');?>" href="<?php echo url_for($table.'/edit?id='.$synonym['record_id']) ?>">
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
                  <a href="#" <?php if($synonym['is_basionym']):?> class="checked"<?php endif;?>></a>
                <?php endif;?>
              </td>
              <td class="widget_row_delete">
                <?php if($synonym['record_id'] == $eid):?>
                  <a class="widget_row_delete" href="<?php echo url_for('synonym/delete?id='.$synonym['id']);?>" title="<?php echo __('Delete Synonym') ?>">
                  <?php echo image_tag('remove.png'); ?>
                  </a>
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
<br />
<?php echo image_tag('add_green.png');?><a title="<?php echo __('Add Synonymies');?>" class="link_catalogue" href="<?php echo url_for('synonym/add?table='.$table.'&id='.$eid); ?>"><?php echo __('Add');?></a>

<script type="text/javascript">

function forceHelper(e,ui)
{
   $(".ui-state-highlight").html("<td colspan='3'>&nbsp;</td>");
}

$(document).ready(function()
{

  $("#synonym td.basio_cell a").click(function ()
  {
    was_basio = false;
    if($(this).hasClass('checked')) was_basio = true;
    clicked_el = $(this);
    s_data = { id:  getIdInClasses($(this).parent().parent()), group_id: getIdInClasses($(this).closest('.widget_sub_table')) };
    
    if(was_basio) s_data['uncheck'] = 'true';
    $.ajax({
      type: "POST",
      url: "<?php echo url_for('synonym/setBasionym?table='.$table.'&rid='.$eid); ?>",
      data: s_data,
      success: function(html) {
        if(html=='ok')
        {
          clicked_el.closest('table').find('.checked').removeClass('checked');
          if(!was_basio)
            clicked_el.addClass('checked');
        }
      }
    });
    return false;
  });

  $("#synonym .widget_sub_table tbody").sortable({
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
        url: "<?php echo url_for('synonym/editOrder?table='.$table.'&rid='.$eid); ?>",
        data: { order: result, synonym_type: $(this).parent().attr('alt') }
      });
    }
  });

});
</script>
