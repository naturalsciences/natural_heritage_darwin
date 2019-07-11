<?php slot('title', __('Register'));  ?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<div class="page">
  <h1><?php echo __("Register");?></h1>
  <?php echo form_tag('register/index', array('id'=>'registration_form'));?>
    <h2 class="title"><?php echo __("Please fill in the application form") ?></h2>
    <div class="borded">
      <table id="registration">
        <tbody>
          <tr>
            <td colspan="3">
              <?php echo $form->renderGlobalErrors() ?>
            </td>
          <tr>
          <tr>
            <th><?php echo $form['is_physical']->renderLabel();?>:</th>
            <td><?php echo $form['is_physical']->render();?></td>
            <td><?php echo $form['is_physical']->renderError();?></td>
          </tr>
          <tr id="is_not_physical">
            <th><?php echo $form['sub_type']->renderLabel() ?>:</th>
            <td>
              <?php echo $form['sub_type']->renderError() ?>
              <?php echo $form['sub_type'] ?>
            </td>
          </tr>
          <tr id="is_physical">
            <th><?php echo $form['gender']->renderLabel() ?>:</th>
            <td>
              <?php echo $form['gender']->renderError() ?>
              <?php echo $form['gender'] ?>
            </td>
          </tr>
          <tr id="is_physical">
            <th><?php echo $form['title']->renderLabel();?>:</th>
            <td><?php echo $form['title']->render();?></td>
            <td><?php echo $form['title']->renderError();?></td>
          </tr>
          <tr>
            <th><?php echo $form['family_name']->renderLabel();?>:</th>
            <td><?php echo $form['family_name']->render();?></td>
            <td><?php echo $form['family_name']->renderError();?></td>
          </tr>
          <tr>
            <th><?php echo $form['given_name']->renderLabel();?>:</th>
            <td><?php echo $form['given_name']->render();?></td>
            <td><?php echo $form['given_name']->renderError();?></td>
          </tr>
          <tr>
            <th><?php echo $form['RegisterLoginInfosForm'][0]['user_name']->renderLabel();?>:</th>
            <td><?php echo $form['RegisterLoginInfosForm'][0]['user_name']->render();?></td>
            <td><?php echo $form['RegisterLoginInfosForm'][0]['user_name']->renderError();?></td>
          </tr>
          <tr>
            <th><?php echo $form['RegisterLoginInfosForm'][0]['new_password']->renderLabel();?>:</th>
            <td><?php echo $form['RegisterLoginInfosForm'][0]['new_password']->render();?>
                <p class="small_message" style="float:right;"><?php echo __("Password must contain at least :<br /> * a case mix<br /> * one digit<br /> * minimum 6 characters length"); ?></p>
            </td>
            <td><?php echo $form['RegisterLoginInfosForm'][0]['new_password']->renderError();?></td>
          </tr>
          <tr>
            <th><?php echo $form['RegisterLoginInfosForm'][0]['confirm_password']->renderLabel();?>:</th>
            <td><?php echo $form['RegisterLoginInfosForm'][0]['confirm_password']->render();?></td>
            <td><?php echo $form['RegisterLoginInfosForm'][0]['confirm_password']->renderError();?></td>
          </tr>
          <tr>
            <th><?php echo $form['RegisterCommForm'][0]['entry']->renderLabel();?>:</th>
            <td><?php echo $form['RegisterCommForm'][0]['entry']->render();?></td>
            <td><?php echo $form['RegisterCommForm'][0]['entry']->renderError();?></td>
          </tr>
          <tr>
            <th><?php echo $form['selected_lang']->renderLabel();?>:</th>
            <td><?php echo $form['selected_lang']->render();?></td>
            <td><?php echo $form['selected_lang']->renderError();?></td>
          </tr>
          <tr>
            <th><?php echo $form['captcha']->renderLabel();?>:</th>
            <td><?php echo $form['captcha']->render();?></td>
            <td><?php echo $form['captcha']->renderError();?></td>
          </tr>
          <tr>
            <th></th>
            <td><?php echo $form['terms_of_use']->render();?>&nbsp;<?php echo __("I accept the ").link_to(__("terms of use"),"board/termOfUse",array('target'=>'_pop')) ; ?></td>
            <td><?php echo $form['terms_of_use']->renderError();?></td>
          </tr>
          <tr>
            <th></th>
            <td colspan="2">
                <div class="check_right">
                  <?php echo $form->renderHiddenFields();?>
                  <input type="submit" name="submit" id="submit" value="<?php echo __('Register'); ?>">
                </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </form>
</div>
<script language="javascript">
  $(document).ready(function () {
    $('form#registration_form').submit(function(){
      $('input#submit').attr('disabled', 'true');
      return true;
    });
  $('#users_is_physical').change(function(){
    if ($(this).val() == 1)
    {
      $('tr#is_not_physical').hide();
      $('tr#is_physical').fadeIn();
      $('label[for="users_family_name"]').html("<?php echo __('Family Name') ; ?>") ;
      $('label[for="users_given_name"]').html("<?php echo __('Given Name'); ?>") ;
    }
    else
    {
      $('tr#is_physical').hide();
      $('tr#is_not_physical').fadeIn();
      $('label[for="users_family_name"]').html("<?php echo __('Name'); ?>") ;
      $('label[for="users_given_name"]').html("<?php echo __('Abbreviation'); ?>") ;
    }
  });
  $('#users_is_physical').change();         
  });
</script>
