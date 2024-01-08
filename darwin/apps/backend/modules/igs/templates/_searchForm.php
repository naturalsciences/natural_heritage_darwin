<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<div class="catalogue_ig">
  <?php echo form_tag('igs/search'.( isset($is_choose) ? '?is_choose='.$is_choose : '').( isset($increment) ? '&increment=on' : '') , array('class'=>'search_form','id'=>'igs_filter'));?>
  <div class="container">
    <table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
      <thead>
        <tr>
          <th><?php echo $form['ig_num']->renderLabel() ?><span style="float:right;"><?php echo __("Exact") ?></span></th>
          <th><?php echo $form['ig_type']->renderLabel() ?></th>
          <th><?php echo $form['from_date']->renderLabel(); ?></th>
          <th><?php echo $form['to_date']->renderLabel(); ?></th>
		  <th><?php echo $form['complete']->renderLabel(); ?></th>
		  <th><?php echo $form['nagoya_status']->renderLabel(); ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $form['ig_num']->render() ?><?php echo $form['ig_num_exact']->render() ?></td>
          <td><?php echo $form['ig_type']->render() ?></td>          
          <td><?php echo $form['from_date']->render() ?></td>
          <td><?php echo $form['to_date']->render() ?></td>
		  <td><?php echo $form['complete']->render() ?></td>
		  <td><?php echo $form['nagoya_status']->render() ?></td>
        </tr>
		<tr>
			<td colspan="6">
				<hr>
			</td>
		</tr>
        <tr>
        <th colspan="2"><?php echo __("People");?></th>
		<th colspan="2"><?php echo __("Collection");?></th>
		<th ><?php echo __("Comment");?></th>
        </tr>
        <tr>	
       
		<?php if(isset($is_choose)) :?>
			<td style="vertical-align: top;" colspan="2">  
			<div class="fuzzy_people"><?php echo $form['people_fuzzy'];?></div></td>
			<td style="vertical-align: top;"><?php echo $form['role_ref'];?></td>
		<?php else: ?>
			<td  style="vertical-align: top;" colspan="2">			
			<input type="button" id='people_switch_fuzzy_0' value="<?php echo __('Precise/fuzzy'); ?>">
			</td>
			</tr>
			<tr>
			<td  style="vertical-align: top;"><div class="precise_people"><?php echo $form['people_ref'];?></div></td>        
			<td class="fuzzy_people_0 hidden" colspan="2"><div class="fuzzy_people"><?php echo $form['people_fuzzy'];?></div></td>
			<td style="vertical-align: top;"><?php echo $form['role_ref'];?></td>
		<?php endif; ?>
		<td style="vertical-align: top;"><div class="treelist"><?php echo $form['collection_ref'];?></div></td> 
		<td style="vertical-align: top;"><?php echo $form['comment_indexed'];?></td> 
        </tr>
		<tr>
			<td colspan="5">
				<hr>
			</td>
		</tr>
        <tr>
         <td><input class="search_submit main_search_ig" type="submit" name="search" value="<?php echo __('Search'); ?>" /></td>
		 <td><input class="search_submit get_tab" type="button" name="search" value="<?php echo __('Get tab-delimited'); ?>"/></td>
		  <?php if($sf_user->isAtLeast(Users::ENCODER)):?> <td><div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for('igs/new') ?>"><?php echo __('New');?></a></div></td><?php endif;?>
        </tr>
      </tbody>
    </table>
    <div class="search_results">
      <div class="search_results_content">
      </div>
    </div>
   
  </div>
</form>
</div>

<script type="text/javascript">
 $(document).ready(function () {
 
  $('.chk input').change(function()
    {
      li = $(this).closest('li');
      if(! $(this).is(':checked'))
        li.find(':checkbox').not($(this)).removeAttr('checked').change();
      else
        li.find(':checkbox').not($(this)).attr('checked','checked').change();
    });
 $('.collapsed').click(function()
    {
        $(this).addClass('hidden');
        $(this).siblings('.expanded').removeClass('hidden');
        $(this).parent().siblings('ul').show();
    });

    $('.expanded').click(function()
    {
        $(this).addClass('hidden');
        $(this).siblings('.collapsed').removeClass('hidden');
        $(this).parent().siblings('ul').hide();
    });
 
 
 $('#people_switch_precise_0').click(function() {

    $('#people_switch_precise_0').attr('disabled','disabled') ;
    $('.fuzzy_people_0').removeAttr('disabled') ;
    $('.precise_people_0').toggle() ;
    $(this).closest('table').find('.people_switch_fuzzy_0').toggle() ;
   
	check_state();
	// $('#specimen_search_filters_Peoples_0_people_ref_name').html("") ;
    // $('#specimen_search_filters_Peoples_0_people_ref').val("") ;
  });

  $('#people_switch_fuzzy_0').click(function() {

    $('#people_switch_fuzzy_0').removeAttr('disabled') ;
    //$('.fuzzy_people_0').attr('disabled','disabled') ;
    $('.precise_people_0').removeAttr('disabled') ;
    $(this).closest('table').find('.fuzzy_people_0').toggle() ;
    $('.fuzzy_people_0').find('input:text').val("") ;
	check_state();
  });
 
   if($('.class_fuzzy_people_0').val() != '')
  {
	tmpVal=$('.class_fuzzy_people_0').val();
    $('#people_switch_fuzzy_0').trigger("click") ;
	$('.class_fuzzy_people_0').val(tmpVal);
  }

 


  function check_state()
  {
			
			if(($(".fuzzy_people_0").is(":visible")))
			{

				var valTmp=$('#searchIg_people_ref_name').text();
				if(valTmp.length>0)
				{
					$('.fuzzy_people_0').find('input:text').val(valTmp);
					$('#searchIg_people_ref_name').html("") ;
					$('#searchIg_people_ref').val("") ;
                    $('.precise_people').hide();
                    $('.fuzzy_people').show();
				}
			}
			else if(($(".precise_people_0").is(":visible")))
			{

				$('.fuzzy_people_0').find('input:text').val("");
                 $('.precise_people').show();
                 $('.fuzzy_people').hide();
                
			}
  }
  
  
  $('.catalogue_ig').choose_form({});
  
  $(".new_link").click( function()
  {
   url = $(this).find('a').attr('href');
   data= $('.search_form').serialize();
   open(url+'?'+data);
    return false;
  });
  
  
   $(".get_tab").click(
	function()
	{
		

		var $tmp=$('form:first');
		
		var new_target="<?php echo url_for('igs/downloadTab') ?>";		
		var $inputs = $('form:first :input');
        var form = document.createElement("form");
		form.hidden=true;
		form.setAttribute("method", "post");
		form.setAttribute("action", new_target);

		form.setAttribute("target", "view");

		
		$inputs.each(function() {
			var hiddenField = document.createElement("input"); 
			
			hiddenField.setAttribute("name", this.name);
			hiddenField.setAttribute("value", $(this).val());
			
			form.appendChild(hiddenField);
		});
		
		document.body.appendChild(form);

		window.open('', 'view');

		form.submit();
	}
  );
  
});
</script>
