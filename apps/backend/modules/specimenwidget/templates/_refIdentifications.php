<script  type="text/javascript">
function forceIdentificationHelper(e,ui)
{
   $(".ui-state-highlight").html("<td colspan='6' style='line-height:"+ui.item[0].offsetHeight+"px'>&nbsp;</td>");
}

function forceIdentifiersHelper(e,ui)
{
   $(".ui-state-highlight").html("<td colspan='3' style='line-height:"+ui.item[0].offsetHeight+"px'>&nbsp;</td>");
}

function reOrderIdent()
{
  $('table#identifications').find('tbody.spec_ident_data:visible').each(function (index, item){
    $(item).find('tr.spec_ident_data input[id$=\"_order_by\"]').val(index+1);
  });
}

function reOrderIdentifiers(tableId)
{
  $('table#'+tableId).find('tbody.spec_ident_identifiers_data:visible').each(function (index, item){
    $(item).find('tr.spec_ident_identifiers_data input[id$=\"_order_by\"]').val(index+1);
  });
}

function addIdentifierValue(people_ref,ref_table)
{
  targetUrl = $(ref_table+' tfoot div.add_code a.hidden').attr('href');
  $.ajax(
  {
    type: "GET",
    url: targetUrl+ (0+$(ref_table+' tbody').length)+'/people_ref/'+people_ref + '/iorder_by/' + ($(ref_table+' .spec_ident_identifiers_data:visible').length+1),
    success: function(html)
    {
      $(ref_table).append(html);
      $(ref_table).find('thead:hidden').show();
      $(ref_table).toggleClass('green_border',true);
    }
  });
  return false;
}
</script>
<table class="property_values" id="identifications">
  <thead style="<?php echo ($form['Identifications']->count() || $form['newIdentification']->count())?'':'display: none;';?>" class="spec_ident_head">
    <tr>
      <th><?php echo $form['ident'];?></th>
      <th><?php echo __('Date'); ?></th>
      <th><?php echo __('Category');?></th>
      <th><?php echo __('Subject'); ?></th>
      <th><?php echo __('Det. St.'); ?></th>
      <th></th>
    </tr>
  </thead>   
    <?php $retainedKey = 0;?>
    <?php foreach($form['Identifications'] as $form_value):?>
      <?php include_partial('specimen/spec_identifications', array('form' => $form_value, 'row_num'=>$retainedKey, 'module'=>$module, 'spec_id'=>$spec_id, 'individual_id'=>$individual_id, 'identification_id'=> $retainedKey));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>
    <?php foreach($form['newIdentification'] as $form_value):?>
      <?php include_partial('specimen/spec_identifications', array('form' => $form_value, 'row_num'=>$retainedKey, 'module'=>$module, 'spec_id'=>$spec_id, 'individual_id'=>$individual_id));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>
  <tfoot>
    <tr>
      <td colspan='6'>
        <div class="add_code">
          <a href="<?php echo url_for($module.'/addIdentification'. (($spec_id == 0) ? '': '?spec_id='.$spec_id.(($individual_id == 0) ? '': '&individual_id='.$individual_id)));?>/num/" id="add_identification"><?php echo __('Add identification');?></a>
        </div>
      </td>
    </tr>
  </tfoot>
</table>
<?php echo javascript_include_tag('catalogue_people.js') ?>
<script  type="text/javascript">

$(document).ready(function () {

    $('#add_identification').click(function()
    {
        hideForRefresh('#refIdentifications');
        parent_el = $(this).closest('table#identifications');
        $.ajax(
        {
          type: "GET",
          url: $(this).attr('href')+ ($('tbody.spec_ident_data').length) + '/order_by/' + ($('tbody.spec_ident_data:visible').length+1),
          success: function(html)
          {
            $(parent_el).append(html);
            $(parent_el).find('thead.spec_ident_head:hidden').show();
            showAfterRefresh('#refIdentifications');
          }
        });
        return false;
    });    

    $("#identifications").sortable({
         placeholder: 'ui-state-highlight',
         handle: '.spec_ident_handle',
         axis: 'y',
         change: function(e, ui) {
           forceIdentificationHelper(e,ui);
         },
         deactivate: function(event, ui) {
           reOrderIdent();
         }
       });
});
</script>
