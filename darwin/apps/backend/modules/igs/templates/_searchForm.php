<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php
	$flagMenu=detect_menu_hidden();
?>
<div class="catalogue_ig">
  <?php echo form_tag('igs/search'.( isset($is_choose) ? '?is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'igs_filter'));?>
  <div class="container">
    <table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
      <thead>
        <tr>
          <th><?php echo $form['ig_num']->renderLabel() ?></th>
          <th><?php echo $form['from_date']->renderLabel(); ?></th>
          <th><?php echo $form['to_date']->renderLabel(); ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $form['ig_num']->render() ?></td>
          <td><?php echo $form['from_date']->render() ?></td>
          <td><?php echo $form['to_date']->render() ?></td>
         
        </tr>        
        <tr>
        <th><?php echo __("People");?></th>
        </tr>
        <tr>
        <td colspan="2">       
        <input type="button" id='people_switch_fuzzy_0' value="<?php echo __('Precise/fuzzy'); ?>">
      </td>
        </tr>
        <tr>
        <td><div class="precise_people"><?php echo $form['people_ref'];?></div></td>        
        <td class="fuzzy_people_0 hidden" colspan="2"><div class="fuzzy_people"><?php echo $form['people_fuzzy'];?></div></td>
        <td><?php echo $form['role_ref'];?></td> 
        </tr>
        <tr>
         <td><input class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" /></td>
        </tr>
      </tbody>
    </table>
    <div class="search_results">
      <div class="search_results_content">
      </div>
    </div>
    <?php if($sf_user->isAtLeast(Users::ENCODER)&&$flagMenu):?> <div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for('igs/new') ?>"><?php echo __('New');?></a></div><?php endif;?>
  </div>
</form>
</div>

<script type="text/javascript">
 $(document).ready(function () {
 
 
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
});
</script>
