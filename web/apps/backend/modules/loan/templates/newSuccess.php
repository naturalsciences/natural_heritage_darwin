<?php slot('title',  __( $form->isNew() ? 'Add loan' : 'Edit loan'));  ?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php use_javascript("print_report.js"); ?>
<?php $action = 'loan/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()) ;?>
<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'loan','eid'=> (! $form->getObject()->isNew() ? $form->getObject()->getId() : null ))); ?>
<div class="page">
    <?php if (isset($err_msg)): ?>
      <div>
        <ul id="error_list" class="error_list">
          <li><?php echo $err_msg ;?></li>
        </ul>
      </div>
    <?php endif; ?>
    <?php include_partial('tabs', array('loan'=> $form->getObject())); ?>
    <div class="tab_content">
      <div>
        <ul id="error_list" class="error_list" style="display:none">
          <li></li>
        </ul>
      </div>   
      <?php echo form_tag($action, array('class'=>'edition loan_form','enctype'=>'multipart/form-data'));?>
        <div class="widgets_container">
          <?php include_partial('widgets/screen', array(
            'widgets' => $widgets,
            'category' => 'loanwidget',
            'options' => array('form' => $form , 'table' => 'loans', 'eid' => $form->getObject()->isNew() ? null : $form->getObject()->getId()),
          )); ?>
        </div>
        <p class="clear"></p>
        <?php include_partial('widgets/float_button', array('form' => $form,
                                                            'module' => 'loan',
                                                            'search_module'=>'loan/index',
                                                            'save_button_id' => 'submit_loan',
                                                            'print_button_id' => 'print_item_'.(($form->getObject()->isNew())?null:$form->getObject()->getId())
                                                    )
        ); ?>
        <p class="form_buttons">
          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('New loan'), 'loan/new') ?>
            &nbsp;<a href="<?php echo url_for('loan/new?duplicate_id='.$form->getObject()->getId());?>" class="duplicate_link"><?php echo __('Duplicate loan');?></a>
            &nbsp;<?php echo link_to(__('Delete'),
                                     'loan/delete?id='.$form->getObject()->getId(),
                                     array('method' => 'delete', 'confirm' => __('Are you sure?'))) ?>
            &nbsp;<?php echo link_to(__('Print loan'),
              'report/getReport?'.http_build_query(array(
                                                     'name'=>'loans_form_complete',
                                                     'default_vals'=>array(
                                                       'loan_id'=>$form->getObject()->getId()
                                                     )
                                                   )),
              array('class'=>'print_item', 'id' => 'print_item_'.$form->getObject()->getId())
            );?>
          <?php endif?>        
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

      $(document).ready(function () {
        $('body').catalogue({});
        $('#submit_loan').click(function() 
        {
          form = $(this).closest('form') ;
          form.removeAttr('target') ;
          form.attr('action', '<?php echo url_for($action) ; ?>') ;
          form.submit() ;
        });
        $("div.widgets_container").print_report({ "q_tip_text" : "<?php echo addslashes(__('Please fill in the criterias to print your report'));?>" });
      });
      </script>   
    </div>
</div>
