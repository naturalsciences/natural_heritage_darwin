<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<script type="text/javascript">
$(document).ready(function () {
    $('form.qtiped_form').modal_screen();
 });
</script>    
<h1 class="edit_mode"><?php echo __("Set rights on widgets for %name%",array('%name%' => $user->getFormatedName())); ?></h1>
<?php echo form_tag('collection/WidgetsRight?user_ref='.$sf_params->get('user_ref').'&collection_ref='.$sf_params->get('collection_ref'), array('class'=>'qtiped_form') );?>
  <table class="widget_right edition">
    <thead>
      <tr>
      	<th><?php echo __("Category/screen");?></th><th><?php echo __("Name");?></th><th><?php echo __("Allow visibility");?></th>
      </tr>
    </thead>
	  <tbody>
      <?php echo $form['widget_ref']->renderError() ?>
      <?php echo $form['widget_ref'] ?>
	  </tbody>
    <tfoot>
      <tr>
        <td colspan="3"> 
          <input id="reset" type="reset" value="<?php echo __('Reset');?>" />
          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>
<script>
$('#all_category').live('change',function()
{
  $(this).closest('tbody').find('input[type=checkbox]').attr("checked",$(this).attr('checked')) ;
});
</script>
