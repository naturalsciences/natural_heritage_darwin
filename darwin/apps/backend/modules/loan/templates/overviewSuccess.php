<?php slot('title', __('Loan Overview'));  ?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php use_javascript('button_ref.js') ?>

<div class="page">
    <h1 class="edit_mode"><?php echo __('Overview');?></h1>

    <?php include_partial('tabs', array('loan'=> $loan, 'items'=>array())); ?>
    <div class="tab_content">

      <?php echo form_tag('loan/overview?id='.$loan->getId()."&page=".$form['current_page']->getValue(), array('class'=>'edition loan_overview_form'));?>
<?php echo $form['nb_specimens']->render();?>
&nbsp;Specimens&nbsp;
<?php echo $form['nb_pages']->render();?>
<?php echo $form['offset']->render();?>
<?php echo $form['current_page']->renderLabel()." : ";?>

<?php echo $form['current_page']->render();?>
&nbsp;

<a href="<?php echo url_for('loan/overview')."?id=".$loan->getId(); ?>" name='go_page' id="go_page"/><?php print(__("Change page"));?></a>
&nbsp;
<br/>
<br/>
      <?php echo $form->renderGlobalErrors();?>
        <table <?php if(! count($form['LoanItems']) && ! count($form['newLoanItems'])) echo 'class="hidden"';?> id="items_table">
        <thead class="loanlines_titles">
          <tr>
		   <!--JMHerpers 2018 04 19-->
            <th></th>
            <th><?php echo __('Item') ;?></th>
            <th><?php echo __('Main Code') ;?></th>
            <th><?php echo __('Taxon');?></th>
            <th><?php echo __('Details') ;?></th>
			<th></th>
            <th><?php echo __('Expedition / Return') ;?></th>
            <th></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
		  
		  <?php $i=(int)$form["offset"]->getValue()+1;?> 	
          <?php foreach($form['LoanItems'] as $name => $sf):?>
            <?php include_partial('loanLine', array('loan'=> $loan, 'form'=>$sf, 'lineObj' => $form->getEmbeddedForm('LoanItems')->getEmbeddedForm($name)->getObject(),"row_id"=>$i)); ?>
			<?php $i=$i+1; ?>
          <?php endforeach;?>

          <?php foreach($form['newLoanItems'] as $name => $sf):?>
            <?php include_partial('loanLine', array('loan'=> $loan, 'form'=>$sf, 'lineObj' => $form->getEmbeddedForm('newLoanItems')->getEmbeddedForm($name)->getObject(),"row_id"=>$i)); ?>
			<?php $i=$i+1; ?>
          <?php endforeach;?>
        </tbody>
       </table>

        <div class="warn_message <?php if(count($form['LoanItems']) ||  count($form['newLoanItems'])) echo 'hidden'?>">
			<?php echo __('There is currently no items in your loan. Do not forget to add them.');?>
		</div>
        <!--ftheeten 2016 11 25--> 
        <div class="form_buttons">         
			<b><?php echo __('Label code');?></b> 
			<?php echo $form['code_part']->render();?>
			<input type="button" style="display:none" class="add_loan_item" value="<?php echo __('Add item'); ?>"></input>
			<?php echo $form['selected_id']->render();?>
        </div>
        <div class="form_buttons">
			<div id="checking" class="hidden">
				<input type="button" id="add_maint_items" value="<?php echo __('Add Maintenance for checked');?>" />
				<input type="button" id="del_checked_items" value="<?php echo __('Delete checked items');?>" />
			</div>
            
            &nbsp;
            <a href="<?php echo url_for('specimen/choosePinned') ?>" id="add_multiple_pin"><?php echo __('Add multiple items');?></a>
            &nbsp;
			<?php echo link_to(__('Back to Loan'), 'loan/edit?id='.$loan->getId()) ?>
			<a href="<?php echo url_for('loan/index') ?>"><?php echo __('Cancel');?></a>
			<input type="button" id="submit_jquery" name="submit_jquery" value="<?php echo __('Add items and save changes');?>" />
			<input value="<?php echo __('Save change');?>"  id="submit" type="submit" />
        </div>
      </form>


<script  type="text/javascript">
var refresh=false;
var url_page="<?php echo url_for('loan/overview')."?id=".$loan->getId(); ?>";
$(document).ready(function () {
	
	function add_item_ajax(loan_ref, item_ref)
	{
		var url="<?php echo url_for('loan/addSpecToLoan'); ?>";
		$.ajax({
		  url: url,
		  data: {'loan_id':loan_ref, "spec_id":item_ref},
		  success: function(data)
		  {
			//console.log(data);
			if(data.status=="true")
			{
				//$("#submit").click();
				 //location.reload();
			}
			//
		  }
		  ,
		  dataType: "json"
		});
	}
	
    //rmca 2018 12 03
	$("#submit_jquery").click(function(){
	
			console.log($(".autocomplete_for_code").val());
			if($(".autocomplete_for_code").val().length>0){
				//$(".add_loan_item").click();
				//add_item_ajax(<?php print($loan->getId()); ?>, $(".catch_selection").val());
				refresh=true;
				addPinned($(".catch_selection").val(), $("#loan_overview_code_part").val());
				$("#submit").click();
							
			}
			else
			{
				$("#submit").click();
			}
	});
	
	//rmca 2018 12 03
	$("#tab_0").click(function(event){
		event.preventDefault();
		console.log("tab_0");
		$("#submit_jquery").click();
		window.open($("#tab_0").attr("href"),"_self");
	})

    $('.select_chk_box').click( function(event) {
      if ($('.select_chk_box:checked').length > 0) $("#checking").show() ;
      else $("#checking").hide() ;
    });

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
            parent_el.append(html);
            $('.warn_message').addClass('hidden');
            showAfterRefresh('.loan_overview_form');
            $('.loan_overview_form').css("z-index",999);

            $('.loan_overview_form > table').removeClass('hidden');

          }
        });
        return false;
    });
    $('#del_checked_items').click(function (event) {
      event.preventDefault();
      var ids = [];
      $('.select_chk_box:checked').each(function (i) {
        ids.push($(this).val());
      });
      $.ajax(
      {
        type: "GET",
        url: '<?php echo url_for("loanitem/deleteChecked");?>/ids/'  + ids,
        success: function(html)
        {
          $('.select_chk_box:checked').each(function (i) {
            parent_el = $(this).closest('tr');
            parent_el.hide();
            parent_el.next().hide();
            parent_el.next().next().hide();
          });
          $("#checking").hide() ;
        }
      });
    });

    $('#add_maint_items').click(function (event) {
      event.preventDefault();
      var ids = [];
      $('.select_chk_box:checked').each(function (i) {
        ids.push($(this).val());
      });

      if(ids.length ==0) return;
      var last_position = $(window).scrollTop();
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
          style: 'ui-tooltip-light ui-tooltip-rounded dialog-modal-edit'
        });
  });
  function addPinned(spec_id, spec_name)
  {
    info = 'ok';
    ref_table = $('.loan_overview_form > table > tbody');
    ref_table.find('tr').each(function() {
      if($(this).find('input[id$=\"_specimen_ref\"]').val() === spec_id) info = 'bad' ;
    });
    if(info != 'ok') return false;
    hideForRefresh('.loan_overview_form') ;
	console.log($(".main_line_loan").length);
    $.ajax(
    {
		
      //ftheeten 2016 06 09 (because issue with "$(ref_table).find('tr').length" on series)
       async: false,
      type: "GET",
      url: '<?php echo url_for('loan/addLoanItem?id='.$loan->getId()) ?>'+ '/num/' + ( 1+$(".main_line_loan").length)+'/specimen_ref/'+spec_id,
      success: function(html)
      {
        ref_table.append(html);
        $('.warn_message').addClass('hidden');
        showAfterRefresh('.loan_overview_form');
        $('.loan_overview_form').css("z-index",999);
        $('.loan_overview_form > table').removeClass('hidden');
		
      }
    });
    return true;
  }
  $(".loan_overview_form").catalogue_people({add_button: '#add_multiple_pin', q_tip_text: 'Choose Darwin Item',update_row_fct: addPinned });
  
  //ftheeten 2016 22 25
  	 var url="<?php echo(url_for('catalogue/codesTaxonAutocompleteForLoans?'));?>";

	  $('.autocomplete_for_code').autocomplete({
     
		source: function (request, response) {
            $.getJSON(url, {
						term : request.term,
						collections: <?php echo($loan->getCollectionRef());?>
					} , 
					function (data) 
						{
					response($.map(data, function (value, key) {
                    return value;
                    }));
			});
		},
        select: function (event, ui) {
				console.log(ui);
                $(".catch_selection").val(ui.item.id)        
        },
		minLength: 2,
		delay: 100
	});
    
    $('.add_loan_item').click(
        function()
        {
            
            addPinned($(".catch_selection").val(),"");
        }
    );
	
	$(".change_page").change(
		function()
		{
			$("#go_page").attr("href", url_page+"&page="+$(".change_page").val());
		}
	);
});


function bind_ext_line(f_name, subf_name) {
  $('#loan_overview_' + f_name + '_' + subf_name + '_specimen_ref').bind('change',function(event) {
      $(this).closest('tr').find('.extd_info').show();
    });

    $('#loan_overview_' + f_name + '_' + subf_name + '_specimen_ref').bind('clear',function(event) {
      $(this).closest('tr').find('.extd_info').hide();
    });

    //INIT on first launch
    if($('#loan_overview_' + f_name + '_' + subf_name + '_specimen_ref').val() == '') {
      $('#loan_overview_' + f_name + '_' + subf_name + '_specimen_ref').closest('tr').find('.extd_info').hide();
    }

    $('#loan_overview_' + f_name + '_' + subf_name + '_specimen_ref').closest('tr').find('.extd_info').mouseover(function(event){
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
            data: { id:   $('#loan_overview_' + f_name + '_' + subf_name + '_specimen_ref').val() }
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
