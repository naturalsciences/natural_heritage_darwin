<?php include_javascripts_for_form($form) ?>
<div id="collections_codes_screen">
<?php echo form_tag('collection/addSpecCodes?id='.$form->getObject()->getId(), array('class'=>'edition qtiped_form', 'id' => 'collections_codes_form') );?>
<table>
  <tbody>
    <tr>
        <td colspan="2">
          <?php echo $form->renderGlobalErrors() ?>
        </td>
    </tr>
    <tr>
      <th><?php echo $form['code_prefix']->renderLabel();?></th>
      <td>
        <?php echo $form['code_prefix']->renderError(); ?>
        <?php echo $form['code_prefix'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['code_prefix_separator']->renderLabel();?></th>
      <td>
        <?php echo $form['code_prefix_separator']->renderError(); ?>
        <?php echo $form['code_prefix_separator'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['code_suffix_separator']->renderLabel();?></th>
      <td>
        <?php echo $form['code_suffix_separator']->renderError(); ?>
        <?php echo $form['code_suffix_separator'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['code_suffix']->renderLabel();?></th>
      <td>
        <?php echo $form['code_suffix']->renderError(); ?>
        <?php echo $form['code_suffix'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['code_auto_increment']->renderLabel();?></th>
      <td>
        <?php echo $form['code_auto_increment']->renderError(); ?>
        <?php echo $form['code_auto_increment'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['code_auto_increment_for_insert_only']->renderLabel();?></th>
      <td>
        <?php echo $form['code_auto_increment_for_insert_only']->renderError(); ?>
        <?php echo $form['code_auto_increment_for_insert_only'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['code_specimen_duplicate']->renderLabel();?></th>
      <td>
        <?php echo $form['code_specimen_duplicate']->renderError(); ?>
        <?php echo $form['code_specimen_duplicate'];?>
      </td>
    </tr>
  </tbody>
  <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields() ?>
          &nbsp;<a href="#" class="cancel_qtip"><?php echo __('Cancel');?></a>
          <?php echo link_to(__('Delete'),'collection/deleteSpecCodes?id='.$form->getObject()->getId(),array('class'=>'delete_button','title'=>__('Are you sure ?')));?>

          <input id="save" name="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
  </tfoot>  
</table>
</form>
<script  type="text/javascript">
$(document).ready(function () {
  $('form.qtiped_form').modal_screen();
});
</script>
</div>
