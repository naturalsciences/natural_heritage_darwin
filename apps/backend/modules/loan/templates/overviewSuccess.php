<?php slot('title', __('Loan Overview'));  ?>
<div class="page">
    <h1 class="edit_mode"><?php echo __('Overview');?></h1>

    <?php include_partial('tabs', array('loan'=> $loan, 'items'=>array())); ?>
    <div class="tab_content">

      <?php echo form_tag('loan/overview?id='.$loan->getId(), array('class'=>'edition loan_overview_form'));?>

        <table>
        <thead>
          <tr>
            <td></td>
            <td><?php echo __('Darwin Part') ;?></td>
            <td><?php echo __('I.g. Num');?></td>
            <td><?php echo __('Details') ;?></td>
            <td><?php echo __('Expedition') ;?></td>
            <td><?php echo __('Return') ;?></td>
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
      </form>

    </div>

</div>