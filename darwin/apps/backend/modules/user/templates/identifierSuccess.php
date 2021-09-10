<?php include_javascripts_for_form($form) ?>
<div id="comment_screen">

<?php echo form_tag('user/identifier?table=users'. ($form->getObject()->isNew() ?  '&id='.$sf_params->get('id'): '&cid='.$form->getObject()->getId()), array('class'=>'edition qtiped_form', 'id' => 'identifier_form'));?>
<table>
  <tbody>
    <tr>
        <td colspan="2">
          <?php echo $form->renderGlobalErrors() ?>
        </td>
    </tr>
    <tr>
      <th><?php echo $form['protocol']->renderLabel();?></th>
      <td>
        <?php echo $form['protocol']->renderError(); ?>
        <?php echo $form['protocol'];?>
      </td>
    <tr>
      <th class="top_aligned"><?php echo $form['value']->renderLabel();?></th>
      <td>
        <?php echo $form['value']->renderError(); ?>
        <?php echo $form['value'];?>
      </td>
    </tr>
  </tbody>
  <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields() ?>
          &nbsp;<a href="#" class="cancel_qtip"><?php echo __('Cancel');?></a>
          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('Delete identifier'),'catalogue/deleteRelated?table=identifiers&id='.$form->getObject()->getId(),array('class'=>'delete_button','title'=>__('Are you sure ?')));?>
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
