<?php include_javascripts_for_form($form) ?>
<div id="comment_screen">

<?php echo form_tag('comment/comment?table='.$sf_params->get('table') . ($form->getObject()->isNew() ?  '&id='.$sf_params->get('id'): '&cid='.$form->getObject()->getId()), array('class'=>'edition qtiped_form', 'id' => 'comment_form'));?>
<table>
  <tbody>
    <tr>
        <td colspan="2">
          <?php echo $form->renderGlobalErrors() ?>
        </td>
    </tr>
    <tr>
      <th><?php echo $form['notion_concerned']->renderLabel();?></th>
      <td>
        <?php echo $form['notion_concerned']->renderError(); ?>
        <?php echo $form['notion_concerned'];?>
      </td>
    <tr>
      <th class="top_aligned"><?php echo $form['comment']->renderLabel();?></th>
      <td>
        <?php echo $form['comment']->renderError(); ?>
        <?php echo $form['comment'];?>
      </td>
    </tr>
  </tbody>
  <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields() ?>
          &nbsp;<a href="#" class="cancel_qtip"><?php echo __('Cancel');?></a>
          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('Delete'),'catalogue/deleteRelated?table=comments&id='.$form->getObject()->getId(),array('class'=>'delete_button','title'=>__('Are you sure ?')));?>
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
