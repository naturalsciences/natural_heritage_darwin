<?php if(!$form->getObject()->isNew() && isset($status) && isset($status[$form->getObject()->getId()]) && $status[$form->getObject()->getId()]->getStatus()=="closed"):?>
  <div class="closed_message"><?php echo __('Loan closed on %date%.',array('%date%'=>$status[$form->getObject()->getId()]->getDate()));?></div>
<?php endif;?>
<table>
  <tbody>
    <?php echo $form->renderGlobalErrors() ?>
    <tr>
      <th><?php echo $form['name']->renderLabel() ?></th>
      <td>
        <?php echo $form['name']->renderError() ?>
        <?php echo $form['name'] ?>
      </td>
      <th><?php echo $form['from_date']->renderLabel() ?></th>
      <td>
        <?php echo $form['from_date']->renderError() ?>
        <?php echo $form['from_date'] ?>
      </td>

      <th></th>
      <td>
      </td>
    </tr>
    <tr>
      <th></th>
      <td></td>

      <th><?php echo $form['to_date']->renderLabel() ?></th>
      <td>
        <?php echo $form['to_date']->renderError() ?>
        <?php echo $form['to_date'] ?>
      </td>


      <th><?php echo $form['extended_to_date']->renderLabel() ?></th>
      <td>
        <?php echo $form['extended_to_date']->renderError() ?>
        <?php echo $form['extended_to_date'] ?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['description']->renderLabel() ?></th>
      <td colspan="5">
        <?php echo $form['description']->renderError() ?>
        <?php echo $form['description'] ?>
      </td>
    </tr>
    <?php if(! $form->getObject()->isNew()):?>
    <tr>
      <td colspan="6">
        <div>
          <a href="#" id="loan_sync" title="<?php echo __('This will take a snapshot of the loan for archiving purposes.');?>">
            <?php echo image_tag('arrow_refresh.png', 'id=arrow_spin');?> <?php echo __('Take a snapshot of the loan.');?>
          </a>
          <div class="last_sync_message">
            <?php $hist = $form->getObject()->fetchHistories();
            if(empty($hist)):?>
              <?php echo __('Never synchronized');?>
            <?php else:?>
              <?php echo __('Last synchronization on %date%', array('%date%'=> strftime("%d/%m/%Y %H:%M", strtotime($hist[0]['date']))));?>
            <?php endif;?>
          </div>
        </div>
        <script type="text/javascript">
          $(document).ready(function () {
            $('#loan_sync').click(function(event)
            {
              event.preventDefault();
              el = $(this);
              var answer = confirm('<?php echo __('Are you sure you want to archive your loan ?');?>');
              if(answer) {
                should_rotate = true;
                rotate();
                $.ajax({
                  url: '<?php echo url_for('loan/sync?id='.$form->getObject()->getId());?>',
                  success: function(html){
                    should_rotate = false;
                  }
                });
              }
            });
            // dirty rotate script \o/
            var count = 0;
            should_rotate = false;
            function rotate() {
              var elem2 = document.getElementById('arrow_spin');
                elem2.style.MozTransform = 'rotate('+count+'deg)';
                elem2.style.WebkitTransform = 'rotate('+count+'deg)';
                if (count==360) { count = 0 }
                count+=45;
                if(should_rotate) window.setTimeout(rotate, 100);
            }
          });
        </script>
      </td>
    </tr>
    <?php endif;?>
  </tbody>
</table>
