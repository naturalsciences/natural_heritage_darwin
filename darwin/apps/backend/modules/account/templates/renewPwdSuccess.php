<h1 id="login"><?php echo __("Renew your password.");?></h1>
<div class="login">
    <?php echo form_tag('account/renewPwd');?>
      <table>
        <tbody>
          <tr>
            <td colspan="2">
              <?php echo $form->renderGlobalErrors() ?>
            </td>
          </tr>
          <tr>
            <th><?php echo $form['new_password']->renderLabel();?></th>
            <td>
              <?php echo $form['new_password']->renderError();?>
              <?php echo $form['new_password'];?>
            </td>
          </tr>
          <tr>
            <th><?php echo $form['confirm_password']->renderLabel();?></th>
            <td>
              <?php echo $form['confirm_password']->renderError();?>
              <?php echo $form['confirm_password'];?>
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="2"><?php echo $form->renderHiddenFields();?><input type="submit" value="<?php echo __("Renew");?>" /></td>
          </tr>
        </tfoot>
      </table>
    </form>
</div>
