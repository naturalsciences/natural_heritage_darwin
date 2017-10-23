<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'loanitem','eid'=> $form->getObject()->getId() )); ?>
<?php slot('title', __('Edit loan item'));  ?>
<?php $action = 'loanitem/update?id='.$form->getObject()->getId() ;?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<div class="page">
    <h1 class="edit_mode"><?php echo __('Edit loan item');?></h1>
    <?php include_partial('loan/tabs', array('loan'=> $form->getObject()->getLoan(), 'item' => $form->getObject())); ?>
    <div class="tab_content">
      <?php echo form_tag($action, array('class'=>'edition loanitem_form','enctype'=>'multipart/form-data'));?>
      <?php if($form->hasGlobalErrors()):?>
        <ul class="spec_error_list">
          <?php foreach ($form->getErrorSchema()->getErrors() as $name => $error): ?>
            <li class="error_fld_<?php echo $name;?>"><?php echo __($error) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif;?>

        <?php include_partial('widgets/screen', array(
          'widgets' => $widgets,
          'category' => 'loanitemwidget',
          'columns' => 1,
          'options' => array('eid' => $form->getObject()->getId(), 'table' => 'loan_items','form' => $form)
          )); ?>
        <p class="clear"></p>
        <?php include_partial('widgets/float_button', array('form' => $form,
                                                            'module' => 'loanitem',
                                                            'search_module'=>'loan/overview?id='.$form->getObject()->getLoanRef(),
                                                            'save_button_id' => 'submit_loan_item',
                                                            'no_new'=>true,
                                                            'no_duplicate'=>true)
        ); ?>
        <p class="form_buttons">
          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('Delete'), 'loanitem/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => __('Are you sure?'))) ?>
          <?php endif?>
          &nbsp;<a href="<?php echo url_for('loan/index') ?>"><?php echo __('Cancel');?></a>
          <input type="submit" value="<?php echo __('Save');?>" id="submit_loan_item"/>
        </p>
      </form>
    </div>
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
      $(document).ready(function () {
        $('body').catalogue({}); 
        $('#submit_loan_item').click(function()
        {
          form = $(this).closest('form') ;
          form.removeAttr('target') ;
          form.attr('action', '<?php echo url_for($action) ; ?>') ;
          form.submit() ;
        });        
      });
    </script>   
</div>
