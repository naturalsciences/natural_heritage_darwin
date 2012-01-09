<?php slot('title', __('Add Loan'));  ?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php include_partial('widgets/list', array('widgets' => $widgets, 'category' => 'loanwidget','eid'=> null )); ?>
<div class="page">
    <?php include_partial('tabs', array('loan'=> $form->getObject())); ?>
    <div class="tab_content">
      <div>
        <ul id="error_list" class="error_list" style="display:none">
          <li></li>
        </ul>
      </div>   
      <?php echo form_tag('loan/create', array('class'=>'edition loan_form'));?>
        <input type="hidden" name="sf_method" value="put"/>
        <div>
          <?php if($form->hasGlobalErrors()):?>
            <ul class="loan_error_list">
              <?php foreach ($form->getErrorSchema()->getErrors() as $name => $error): ?>
                <li class="error_fld_<?php echo $name;?>"><?php echo __($error) ?></li>
              <?php endforeach; ?>
            </ul>
          <?php endif;?>

          <?php include_partial('widgets/screen', array(
            'widgets' => $widgets,
            'category' => 'loanwidget',
            'options' => array('form' => $form/*, 'level' => 1*/),
          )); ?>
        </div>
        <p class="clear"></p>
        <p class="form_buttons">
          &nbsp;<a href="<?php echo url_for('loan/index') ?>"><?php echo __('Cancel');?></a>
          <input type="submit" value="<?php echo __('Save');?>" id="submit_loan"/>
        </p>
      </form>
      <script  type="text/javascript">

      function addError(html)
      {
        $('ul#error_list').find('li').text(html);
        $('ul#error_list').show();
      }

      function removeError()
      {
        $('ul#error_list').hide();
        $('ul#error_list').find('li').text(' ');
      }   
      </script>   
    </div>
</div>
