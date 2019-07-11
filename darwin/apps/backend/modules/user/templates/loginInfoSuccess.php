<?php include_javascripts_for_form($form) ?>
<?php slot('title',__('Add a new login'));  ?>     
<div id="login_info_screen">
<?php echo form_tag('user/loginInfo?user_ref='.$sf_params->get('user_ref') . ($form->getObject()->isNew() ? '': '&id='.$form->getObject()->getId()), array('class'=>'edition qtiped_form', 'id'=>'login_info_form'));?>

<?php echo $form['user_ref'];?>
<table>
  <tbody>
    <tr>
        <td colspan="2">
          <?php if(!$form->getObject()->isNew()) : ?>
          	<?php echo $form['id'] ; ?>
          <?php endif ; ?>
          <?php echo $form->renderGlobalErrors() ?>
        </td>
    </tr>
    <tr>
      <th><?php echo $form['login_type']->renderLabel();?></th>
      <td>
        <?php if(! $form->getObject()->isNew()) : ?>
              <?php echo $form['login_type']->getValue() ?>
        <?php endif ; ?>
        <?php echo $form['login_type']->renderError(); ?>
    	   <?php echo $form['login_type'];?>        
      </td>
    </tr>
    <tr>
      <th><?php echo $form['user_name']->renderLabel();?></th>
      <td>
        <?php if(! $form->getObject()->isNew()) : ?>
              <?php echo $form['user_name']->getValue() ?>
        <?php endif ; ?>
        <?php echo $form['user_name']->renderError(); ?>
        <?php echo $form['user_name'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['new_password']->renderLabel();?></th>
      <td>
        <?php echo $form['new_password']->renderError(); ?>
        <?php echo $form['new_password'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['confirm_password']->renderLabel();?></th>
      <td>
        <?php echo $form['confirm_password']->renderError(); ?>
        <?php echo $form['confirm_password'];?>
      </td>
    </tr>    
  </tbody>
  <tfoot>
    <tr>
      <td colspan="2">
        <a href="#" class="cancel_qtip"><?php echo __('Cancel');?></a>
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
