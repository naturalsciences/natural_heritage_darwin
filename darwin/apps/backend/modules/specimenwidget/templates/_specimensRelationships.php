<table class="property_values" id="identifications">
  <thead style="<?php echo ($form['SpecimensRelationships']->count() || $form['newSpecimensRelationships']->count())?'':'display: none;';?>">
  </thead>
    <?php $retainedKey = 0;?>
    <?php foreach($form['SpecimensRelationships'] as $form_value):?>  
      <?php include_partial('specimen/specimens_relationships', array('form' => $form_value, 'rownum'=>$retainedKey));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>
    <?php foreach($form['newSpecimensRelationships'] as $form_value):?>
      <?php include_partial('specimen/specimens_relationships', array('form' => $form_value, 'rownum'=>$retainedKey));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>  
  <tfoot>
    <tr>
      <td colspan='5'>
        <div class="add_code">
          <a href="<?php echo url_for('specimen/addSpecimensRelationships'. ($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>/num/" id="add_relship"><?php echo __('Add element');?></a>
        </div>
      </td>
    </tr>
  </tfoot>
</table>
<br/><br/>
<a  target="_blank" href="<?php print(url_for("specimensearch/search/is_choose/",true)."/1?".http_build_query(array("specimen_search_filters[related_ref]"=>$eid)));?>">View all related specimens</a>
<?php echo $form['SpecimensRelationships_holder'];?>
<script  type="text/javascript">
$(document).ready(function () {

    $('#add_relship').click(function()
    {
        hideForRefresh('#SpecimensRelationships');

        parent_el = $(this).closest('table.property_values');
        $.ajax(
        {
          type: "GET",
          url: $(this).attr('href')+ (0+$(parent_el).find('tbody').length),
          success: function(html)
          {
            $(parent_el).append(html);
            $(parent_el).find('thead:hidden').show();
            showAfterRefresh('#SpecimensRelationships');
          }
        });
        return false;
    });

});
</script>
