<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php echo form_tag('search/view?id='.$id, array('id'=>'send_suggestion'));?> 
  <div class="borded right_padded">
    <table>
      <tr>
        <td colspan="4">
          <?php echo $form->renderGlobalErrors() ; ?>
          <?php echo $form['captcha']->renderError() ; ?>
        </td>
      </tr>
      <tr>
        <td><span class="pager_nav"><?php echo $form['formated_name']->renderLabel() ; ?></span></td>
        <td><span class="pager_nav"><?php echo $form['formated_name']->render() ; ?></span></td>          
        <td><span class="pager_nav"><?php echo $form['email']->renderLabel() ; ?></span></td>
        <td><span class="pager_nav"><?php echo $form['email']->render() ; ?></span></td>   
      </tr>      
      <tr>
        <td><span class="pager_nav"><?php echo $form['comment']->renderLabel() ; ?></span></td>
        <td><span class="pager_nav"><?php echo $form['comment']->render() ; ?></span></td>
        <td><span class="pager_nav"><?php echo $form['captcha']->renderLabel() ; ?></span></td>
        <td><span class="pager_nav"><div id="captchadiv"><?php echo $form['captcha']->render() ; ?></div></span></td>
      </tr>
      <tr>
        <td colspan="4" class="right_aligned">
          <?php echo $form->renderHiddenFields();?>
          <input class="search_submit" type="submit" name="submit" value="<?php echo __('Submit'); ?>" />
        </td>
      </tr>
    </table>
  </div>   
  <script type="text/javascript" src="https://www.google.com/recaptcha/api.js"></script>
  <script type="text/javascript">
  $(document).ready(function() {
    $('.search_submit').click(function(event) {
      event.preventDefault() ;
      $.ajax({
        type: "POST",
        url: $('#send_suggestion').attr('action'),
        data: $('#send_suggestion').serialize(),
        success: function(html) {
          $(".suggestion_zone").html(html);
        }
      });
      
      $(".suggestion_zone").html('<img src="/images/loader.gif" />');      
    });
  }) ;
</script>
</form>

