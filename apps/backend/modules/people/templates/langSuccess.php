<?php include_javascripts_for_form($form) ?>
<div id="lang_screen">
<?php echo form_tag('people/lang?ref_id='.$sf_request->getParameter('ref_id') . ($form->getObject()->isNew() ? '': '&id='.$form->getObject()->getId() ), array('class'=>'edition qtiped_form','id'=>'lang_form'));?>

<?php echo $form['people_ref'];?>
<table>
  <tbody>
    <tr>
        <td colspan="2">
          <?php echo $form->renderGlobalErrors() ?>
        </td>
    </tr>
    <tr>
      <th><?php echo $form['language_country']->renderLabel();?></th>
      <td>
        <?php echo $form['language_country']->renderError(); ?>
        <?php echo $form['language_country'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['mother']->renderLabel();?></th>
      <td>
        <?php echo $form['mother']->renderError(); ?>
        <?php echo $form['mother'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['preferred_language']->renderLabel();?></th>
      <td>
        <?php echo $form['preferred_language']->renderError(); ?>
        <?php echo $form['preferred_language'];?>
      </td>
    </tr>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="2">
        <a href="#" class="cancel_qtip"><?php echo __('Cancel');?></a>
        <?php if(! $form->getObject()->isNew()):?>
          <?php echo link_to(__('Delete'),'catalogue/deleteRelated?table=people_languages&id='.$form->getObject()->getId(),array('class'=>'delete_button','title'=>__('Are you sure ?')));?>
        <?php endif;?>
        <input id="submit" type="submit" value="<?php echo __('Save');?>" />
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
