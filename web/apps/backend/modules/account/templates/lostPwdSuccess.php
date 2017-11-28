<h1 id="login"><?php echo __("Reset your account.");?></h1>
<div class="login">
    <?php echo form_tag('account/lostPwd');?>
      <table>
        <tbody>
          <tr>
            <td colspan="2">
              <?php echo $form->renderGlobalErrors() ?>
            </td>
          </tr>
          <tr>
            <th><?php echo $form['user_name']->renderLabel();?></th>
            <td>
              <?php echo $form['user_name']->renderError();?>
              <?php echo $form['user_name'];?>
            </td>
          </tr>
          <tr>
            <th><?php echo $form['user_email']->renderLabel();?></th>
            <td>
              <?php echo $form['user_email']->renderError();?>
              <?php echo $form['user_email'];?>
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="2"><input type="submit" value="<?php echo __("Send me a 'reset account' mail");?>" /></td>
          </tr>
        </tfoot>
      </table>
    </form>
</div>
