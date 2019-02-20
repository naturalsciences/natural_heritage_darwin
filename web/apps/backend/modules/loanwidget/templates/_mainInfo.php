<?php if(!$form->getObject()->isNew() && isset($status) && isset($status[$form->getObject()->getId()]) && $status[$form->getObject()->getId()]->getStatus()=="closed"):?>
  <div class="closed_message"><?php echo __('Loan closed on %date%.',array('%date%'=>$status[$form->getObject()->getId()]->getDate()));?></div>
<?php endif;?>
<table>
  <tbody>
    <?php echo $form->renderGlobalErrors() ?>
    <tr>
	<!--pvignaux 2016 06 14-->
	<th><?php echo $form['collection_ref']->renderLabel() ?></th>

	<td><?php echo $form['collection_ref']->renderError() ?><?php echo $form['collection_ref'] ?></td>

   
        <th class="hide_helper_name" style="display:none;"><?php echo __('Last value in collection') ?></th>
        <td>
            <div class="last_loan_id_in_collection"></div>
        </td>
        <td class="hide_helper_name" style="display:none;">
            <input type="button"  class="copy_loan_id_in_collection"  style="display:hide" value='<?php echo __('Paste to name of loan');?>'></input>
        </td>
    </tr>
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
	<script type="text/javascript">
		//ftheeten 2016 06 14
		
        var url="<?php echo(url_for('catalogue/nameForLoan?'));?>";
		$(document).ready(function () {
			$(".rmca_coll_4_loan").change(
			function()
			{
				
				               
                $.getJSON(url, 
					{
						coll_nr: $(this).val()
					} , 
					function (data) 
					{
						if(data[0])
                        {
                            var lastcode=data[0].name_loan;
                        }
						$(".last_loan_id_in_collection").text(lastcode);
                        $(".hide_helper_name").show();
					}
				);
			}

		);
        
        <?php if(sfContext::getInstance()->getActionName()=="new"||sfContext::getInstance()->getActionName()=="edit"):?>
        
                
                $.reverse_year_in_select("#loans_from_date_year");
                $.reverse_year_in_select("#loans_to_date_year");
                $.reverse_year_in_select("#loans_extended_to_date_year");
         <?php endif;?>
             function pad (str, max, prefix) {
                str = str.toString();
                    return str.length < max ? pad(prefix.concat(str), max, prefix) : str;
                }
         
            $(".copy_loan_id_in_collection").click(
            function()
            {
                var name_loan=$(".last_loan_id_in_collection").text();
                var find=/\d+(?!.*\d)/
                var pattern=name_loan.match(find);
                var len_pattern=pattern.toString().length;
               
                if(pattern)
                {
                    var pos=find.exec(name_loan);
                  
                    new_pattern=(parseInt(pattern))+1;
                    var new_pattern2=pad(new_pattern, len_pattern,"0");
                    name_loan=name_loan.substring(0,pos.index).concat(new_pattern2);
                }
                $(".loan_class").val(name_loan);
            }
            
         );
         
		});
	</script>
  </tbody>
</table>
