<h1 id="login"><?php echo __("This application requires an authentification.");?></h1>
<div class="login">
    <?php echo form_tag('account/login');?>
      <table>
        <tbody>
          <tr>
            <td colspan="2">
              <?php echo $form->renderGlobalErrors() ?>
            </td>
          </tr>
          <tr>
            <th><?php echo $form['username']->renderLabel();?></th>
            <td>
              <?php echo $form['username']->renderError();?>
              <?php echo $form['username'];?>
            </td>
          </tr>
          <tr>
            <th><?php echo $form['password']->renderLabel();?></th>
            <td>
              <?php echo $form['password']->renderError();?>
              <?php echo $form['password'];?>
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="2"><input type="submit" value="<?php echo __('Log in');?>" /></td>
          </tr>
        </tfoot>
      </table>
    </form>
    <hr></hr>
    <table id="login_register">
      <tbody>
        <tr>
          <td><?php echo __("If you don't have an account yet, please");?>&nbsp;<?php echo link_to(__('register'), $sf_context->getConfiguration()->generatePublicUrl('register', array(),$sf_request), array('id'=>'register_link'));?></td>
        </tr>
        <tr>
          <td><?php echo __("If you've lost your password, please");?>&nbsp;<?php echo link_to(__('reset your password'), 'account/lostPwd', array('id'=>'login_lostpwd'));?></td>
        </tr>
      </tbody>
    </table>
</div>
