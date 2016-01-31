<?php include_javascripts_for_form($form) ?>
<div id="catalogue_relation_screen">

<?php echo form_tag('catalogue/relation?table='.$sf_params->get('table').'&rid='.$sf_params->get('rid').'&type='.$sf_params->get('type').($form->getObject()->isNew() ? '': '&id='.$form->getObject()->getId() ), array('class'=>'edition qtiped_form', 'id' => 'relation_form') );?>
<?php echo $form->renderHiddenFields();?>
<table>
  <tbody>
    <tr>
        <td colspan="2">
          <?php echo $form->renderGlobalErrors() ?>
        </td>
    </tr>
    <tr>
      <th><?php echo $form['record_id_2']->renderLabel( ($form->getObject()->getRelationshipType() == 'current_name')? 'Renamed in': 'Recombined from' );?></th>
      <td>
        <?php echo $form['record_id_2']->renderError(); ?>
        <?php echo $form['record_id_2'];?>
      </td>
    </tr>
    <tr>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="2">
        <a href="#" class="cancel_qtip"><?php echo __('Cancel');?></a>
        <?php if(! $form->getObject()->isNew()):?>
          <?php echo link_to(__('Delete'),'catalogue/deleteRelated?table=catalogue_relationships&id='.$form->getObject()->getId(),array('class'=>'delete_button','title'=>__('Are you sure ?')));?>
        <?php endif;?> 
        <input id="submit" type="submit" value="<?php echo __('Save');?>" />
      </td>
    </tr>
  </tfoot>
</table>

</form>
<script  type="text/javascript">
   $(document).ready(function () {
    $('form.qtiped_form').modal_screen();

    $('.result_choose').live('click',function(event) {
       event.preventDefault();
       el = $(this).closest('tr');
       $("#catalogue_relationships_record_id_2").val(getIdInClasses(el));
       $("#catalogue_relationships_record_id_2_name").val(el.find('.item_name').text()).show();
       $('.reference_clear').show();
       $('div.search_box, ul.tab_choice').slideUp();
    });
});
</script>

<div class="search_box">
  <?php include_partial('catalogue/chooseItem', array('searchForm' => $searchForm,'is_choose'=>true)) ?>
</div>

</div>
