<?php include_partial('widgets/list', array('widgets' => $widgets, 'category' => 'loanitems','eid'=> $form->getObject()->getId() )); ?>
<?php slot('title', __('Edit Loan Item'));  ?>
<div class="page">
    <h1 class="edit_mode"><?php echo __('Edit Loan Item');?></h1>
    <?php include_partial('loan/tabs', array('loan'=> $form->getObject()->getLoan(), 'item' => $form->getObject())); ?>
    <div class="tab_content">
      <?php echo form_tag('loan/create', array('class'=>'edition loan_form'));?>

        <?php include_partial('widgets/screen', array(
          'widgets' => $widgets,
          'category' => 'loanitem',
          'columns' => 1,
          'options' => array('eid' => $form->getObject()->getId(), 'table' => 'loans')
          )); ?>
        <p class="clear"></p>
        <p class="form_buttons">
          &nbsp;<a href="<?php echo url_for('loan/index') ?>"><?php echo __('Cancel');?></a>
          <input type="submit" value="<?php echo __('Save');?>" id="submit_loan"/>
        </p>
      </form>
    </div>
</div>
