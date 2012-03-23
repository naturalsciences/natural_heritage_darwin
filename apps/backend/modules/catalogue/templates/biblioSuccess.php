<?php include_javascripts_for_form($form) ?>
<div id="catalogue_biblio_screen">

<?php echo form_tag('catalogue/biblio?table='.$sf_params->get('table').'&rid='.$sf_params->get('rid').($form->getObject()->isNew() ? '': '&id='.$form->getObject()->getId() ), array('class'=>'edition qtiped_form', 'id' => 'biblio_form') );?>
<?php echo $form->renderHiddenFields();?>
<table>
  <tbody>
    <tr>
        <td colspan="2">
          <?php echo $form->renderGlobalErrors() ?>
        </td>
    </tr>
    <tr>
      <th><?php echo $form['bibliography_ref']->renderLabel();?></th>
      <td>
        <?php echo $form['bibliography_ref']->renderError(); ?>
        <?php echo $form['bibliography_ref'];?>
      </td>
    </tr>
    <tr>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="2">
        <a href="#" class="cancel_qtip"><?php echo __('Cancel');?></a>
        <?php if(! $form->getObject()->isNew()):?>
          <?php echo link_to(__('Delete'),'catalogue/deleteRelated?table=catalogue_bibliography&id='.$form->getObject()->getId(),array('class'=>'delete_button','title'=>__('Are you sure ?')));?>
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
       $("#catalogue_bibliography_bibliography_ref").val(getIdInClasses(el));
       $("#catalogue_bibliography_bibliography_ref_name").val(el.find('.item_name').text()).show();
       $('.reference_clear').show();
       $('div.search_box, ul.tab_choice').slideUp();
    });
});
</script>

<div class="search_box">
  <?php // include_partial('bibliography/choose', array('searchForm' => $searchForm,'is_choose'=>true)) ?>
  <?php include_partial('bibliography/searchForm', array('form' => $searchForm, 'is_choose' => true)) ?>
</div>

</div>
