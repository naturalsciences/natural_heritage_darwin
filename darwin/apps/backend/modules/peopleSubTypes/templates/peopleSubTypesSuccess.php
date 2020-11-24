<?php include_javascripts_for_form($form) ?>
<div id="people_sub_type_screen">

<?php echo form_tag('peopleSubTypes/peopleSubTypes?' . ($form->getObject()->isNew() ?  'id='.$sf_params->get('id'): '&cid='.$form->getObject()->getId()), array('class'=>'edition qtiped_form', 'id' => 'people_sub_types_form'));?>
<table>
  <tbody>
    <tr>
        <td colspan="2">
          <?php echo $form->renderGlobalErrors() ?>
        </td>
    </tr>
    <tr>
      <th><?php echo $form['sub_type']->renderLabel();?></th>
      <td>
        <?php echo $form['sub_type']->renderError(); ?>
        <?php echo $form['sub_type'];?>
      </td>    
  </tbody>
  <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields() ?>
          &nbsp;<a href="#" class="cancel_qtip"><?php echo __('Cancel');?></a>
          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('Delete sub type'),'catalogue/deleteRelated?table=people_sub_types&id='.$form->getObject()->getId(),array('class'=>'delete_button','title'=>__('Are you sure ?')));?>
          <?php endif; ?>
          <input id="save" name="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
  </tfoot>  
</table>
<script  type="text/javascript">
$(document).ready(function () {
    $('form.qtiped_form').modal_screen();

});
</script>
</form>

</div>
