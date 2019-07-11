  <div class="blue_line">
    <table>
      <tr>
        <td class="blue_line_left">
        </td>
        <td class="blue_line_right">
          <?php echo form_tag('register/login', array('class'=>'register_form'));?>
            <table>
              <tr>
                <?php if(! $sf_user->isAuthenticated()):?>
                  <td class="field"><?php echo $form['username']->render(array('class'=>'small','title'=>__('Username') )); ?></td>
                  <td class="field"><?php echo $form['password']->render(array('class'=>'small','title'=>__('Password'))); ?></td>
                  <td id="login_bt"><?php echo $form['_csrf_token'] ; ?>
                    <input type="submit" value="&gt;&gt;">
                  </td>
                  <td class="menu_button"><?php echo link_to(__('Register'),'register/index') ;?></td>
                <?php else:?>
                  <td class="menu_button"><a href="backend.php">Go to backend</a></td>
                  <td class="menu_button"><?php echo link_to(__('Logout'),'register/logout') ;?></td>
                <?php endif;?>
              </tr>
            </table>
          </form>
        </td>
      </tr>
    </table>
  </div>
