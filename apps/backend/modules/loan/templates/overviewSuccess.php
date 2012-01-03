<?php slot('title', __('Loan Overview'));  ?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<div class="page">
    <h1 class="edit_mode"><?php echo __('Overview');?></h1>

    <?php include_partial('tabs', array('loan'=> $loan, 'items'=>array())); ?>
    <div class="tab_content">

      <?php echo form_tag('loan/overview?id='.$loan->getId(), array('class'=>'edition loan_overview_form'));?>


        <table>
        <thead>
          <tr>
            <th></th>
            <th><?php echo __('Darwin Part') ;?></th>
            <th><?php echo __('I.g. Num');?></th>
            <th><?php echo __('Details') ;?></th>
            <th><?php echo __('Expedition') ;?></th>
            <th><?php echo __('Return') ;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($form['LoanItems'] as $sf):?>
            <?php include_partial('loanLine', array('loan'=> $loan, 'form'=>$sf)); ?>
          <?php endforeach;?>

          <?php foreach($form['newLoanItems'] as $sf):?>
            <?php include_partial('loanLine', array('loan'=> $loan, 'form'=>$sf)); ?>
          <?php endforeach;?>
        </tbody>
       </table>


          <?php echo link_to(__('Back to Loan'), 'loan/edit?id='.$loan->getId()) ?>
          <a href="<?php echo url_for('loan/index') ?>"><?php echo __('Cancel');?></a>

          <a href="<?php echo url_for('loan/addLoanItem?id='.$loan->getId()) ?>" id="add_item"><?php echo __('Add item');?></a>
          <input id="submit" type="submit" value="<?php echo __('Save');?>" />

      </form>


<script  type="text/javascript">
$(document).ready(function () {

    $('#add_item').click( function(event)
    {
        event.preventDefault();
        hideForRefresh('.loan_overview_form');
        parent_el = $('.loan_overview_form table tbody');
        $.ajax(
        {
          type: "GET",
          url: $(this).attr('href')+ '/num/' + ( parent_el.find('tr').length),
          success: function(html)
          {                    
            //console.log(parent_el);
            parent_el.append(html);
            showAfterRefresh('.loan_overview_form');
            $('.loan_overview_form').css({position: 'absolute'});
 
          }
        });
        return false;
    }); 
});
</script>


    </div>

</div>