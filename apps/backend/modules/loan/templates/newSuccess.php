<?php slot('title',  __( $form->isNew() ? 'Add Loan' : 'Edit Loan'));  ?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php include_partial('widgets/list', array('widgets' => $widgets, 'category' => 'loan','eid'=> (! $form->getObject()->isNew() ? $form->getObject()->getId() : null ))); ?>
<div class="page">
    <?php include_partial('tabs', array('loan'=> $form->getObject())); ?>
    <div class="tab_content">
      <div>
        <ul id="error_list" class="error_list" style="display:none">
          <li></li>
        </ul>
      </div>   
      <?php echo form_tag('loan/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()), array('class'=>'edition loan_form'));?>
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
          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('New loan'), 'loan/new') ?>
            &nbsp;<a href="<?php echo url_for('loan/new?duplicate_id='.$form->getObject()->getId());?>" class="duplicate_link"><?php echo __('Duplicate loan');?></a>
            &nbsp;<?php echo link_to(__('Delete'), 'loan/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => __('Are you sure?'))) ?>
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
      </script>   
    </div>
</div>
