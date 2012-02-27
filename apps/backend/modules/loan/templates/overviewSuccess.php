<?php slot('title', __('Loan Overview'));  ?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php use_javascript('button_ref.js') ?>

<div class="page">
    <h1 class="edit_mode"><?php echo __('Overview');?></h1>

    <?php include_partial('tabs', array('loan'=> $loan, 'items'=>array())); ?>
    <div class="tab_content">

      <?php echo form_tag('loan/overview?id='.$loan->getId(), array('class'=>'edition loan_overview_form'));?>

      <?php echo $form->renderGlobalErrors();?>
        <table <?php if(! count($form['LoanItems']) && ! count($form['newLoanItems'])) echo 'class="hidden"';?>>
        <thead>
          <tr>
            <th></th>
            <th><?php echo __('Darwin Part') ;?></th>
            <th><?php echo __('I.g. Num');?></th>
            <th><?php echo __('Details') ;?></th>
            <th><?php echo __('Expedition') ;?></th>
            <th><?php echo __('Return') ;?></th>
            <th></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($form['LoanItems'] as $name => $sf):?>
            <?php include_partial('loanLine', array('loan'=> $loan, 'form'=>$sf, 'lineObj' => $form->getEmbeddedForm('LoanItems')->getEmbeddedForm($name)->getObject())); ?>
          <?php endforeach;?>

          <?php foreach($form['newLoanItems'] as $name => $sf):?>
            <?php include_partial('loanLine', array('loan'=> $loan, 'form'=>$sf, 'lineObj' => $form->getEmbeddedForm('LoanItems')->getEmbeddedForm($name)->getObject())); ?>
          <?php endforeach;?>
        </tbody>
       </table>
       
        <div class="warn_message <?php if(count($form['LoanItems']) ||  count($form['newLoanItems'])) echo 'hidden'?>">
          <?php echo __('There is currently no items in your loan. Do not forget to add them.');?></div>
  
        <div class="form_buttons">
          <input type="button" id="add_maint_items" value="<?php echo __('Add Maintenance for checked');?>" />
          <a href="<?php echo url_for('loan/addLoanItem?id='.$loan->getId()) ?>" id="add_item"><?php echo __('Add item');?></a>
          &nbsp;

          <?php echo link_to(__('Back to Loan'), 'loan/edit?id='.$loan->getId()) ?>
          <a href="<?php echo url_for('loan/index') ?>"><?php echo __('Cancel');?></a>

          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </div>
        
      </form>


<script  type="text/javascript">
$(document).ready(function () {
    $('#add_item').click( function(event)
    {
        event.preventDefault();
        hideForRefresh('.loan_overview_form');
        parent_el = $('.loan_overview_form > table > tbody');
        $.ajax(
        {
          type: "GET",
          url: $(this).attr('href')+ '/num/' + ( parent_el.find('tr').length),
          success: function(html)
          {                    
            //console.log(parent_el);
            parent_el.append(html);
            $('.warn_message').addClass('hidden');
            showAfterRefresh('.loan_overview_form');
            $('.loan_overview_form').css({position: 'absolute'});

            $('.loan_overview_form > table').removeClass('hidden');
 
          }
        });
        return false;
    }); 

    $('#add_maint_items').click(function (event) {
      event.preventDefault();
      var ids = [];
      $('.select_chk_box:checked').each(function (i) {
        ids.push($(this).val());
      });

      if(ids.length ==0) return;
      var last_position = $('body').scrollTop() ;
      scroll(0,0) ;
      $('#add_maint_items').qtip({
          id: 'modal',
          content: {
            text: '<img src="/images/loader.gif" alt="loading"> loading ...',
            title: { button: true, text: '<?php echo __('Add Maintenance')?>' },
            ajax: {
              url: '<?php echo url_for("loanitem/maintenances");?>/ids/'  + ids,
              type: 'GET'
            }
          },
        position: {
          my: 'top center',
          at: 'top center',
          adjust:{
            y: 250 // option set in case of the qtip become too big
          },         
          target: $(document.body),
        },
          
          show: {
            ready: true,
            delay: 0,
            event: event.type,
            solo: true,
            modal: {
              on: true,
              blur: false
            },
          },
          hide: {
            event: 'close_modal',
            target: $('body')
          },
          events: {
            hide: function(event, api) {                
              scroll(0,last_position);
              api.destroy();
            }
          },
          style: 'ui-tooltip-light ui-tooltip-rounded'
        });
  });

});


function bind_ext_line(f_name, subf_name) {
  $('#loan_overview_' + f_name + '_' + subf_name + '_part_ref').bind('change',function(event) {
      $(this).closest('tr').find('.extd_info').show();
    });

    $('#loan_overview_' + f_name + '_' + subf_name + '_part_ref').bind('clear',function(event) {
      $(this).closest('tr').find('.extd_info').hide();
    });

    //INIT on first launch
    if($('#loan_overview_' + f_name + '_' + subf_name + '_part_ref').val() == '') {
      $('#loan_overview_' + f_name + '_' + subf_name + '_part_ref').closest('tr').find('.extd_info').hide();
    }

    $('#loan_overview_' + f_name + '_' + subf_name + '_part_ref').closest('tr').find('.extd_info').mouseover(function(event){
      $(this).qtip({
        show: {
          ready: true,
          delay: 0,
          event: event.type,
          solo: true,
        },
        //hide: { event: 'mouseout' },
        style: {
          tip: true, // Give it a speech bubble tip with automatic corner detection
          name: 'cream'
        },
        content: {
          text: '<img src="/images/loader.gif" alt="loading"> Loading ...',
          title: { text: '<?php echo __("Linked Info") ; ?>' },
          ajax: {
            url: '<?php echo url_for("loan/getPartInfo");?>',
            type: 'GET',
            data: { id:   $('#loan_overview_' + f_name + '_' + subf_name + '_part_ref').val() }
          }
        },
        events: {
          hide: function(event, api) {
            api.destroy();
          }
        }
      });
    });
}
</script>


    </div>

</div>