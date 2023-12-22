<?php slot('title', __( $form->isNew() ? 'Add specimen(s)' : 'Edit Specimen'));  ?>

<script type="text/javascript">
$(document).ready(function ()
{
    check_screen_size() ;
    $(window).resize(function(){
      check_screen_size();
    });
    $('.widget .widget_content:hidden .error_list:has(li)').each(function(){
        $(this).closest('.widget').find('.widget_bottom_button').click();
    });

    $('.spec_error_list li.hidden').each(function(){
        field = getElInClasses($(this),'error_fld_');
        if( $('#specimen_'+field).length == 0 )
            $(this).show();
    });

  $('.pin_but').click(function(e){
    e.preventDefault();
    if($(this).hasClass('pin_on'))
    {
      $(this).parent().find('.pin_off').removeClass('hidden');
      $(this).addClass('hidden') ;
      pin_status = 0;
    }
    else
    {
      $(this).parent().find('.pin_on').removeClass('hidden');
      $(this).addClass('hidden') ;
      pin_status = 1;
    }
    $.get( $(this).parent().attr('href') + '/status/' + pin_status,function (html){});
  });

});
</script>

<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'specimen','eid'=> (! $form->getObject()->isNew() ? $form->getObject()->getId() : null ))); ?>

<div class="encoding">
  <div class="page">

<?php if($form->isNew()):?>
  <h3 class="spec"><span class="title"><?php echo __( 'Add specimen(s)');?></span></h3>
<?php else:?>
  <h3 class="spec">
  <span class="title"><?php echo __('Edit Specimen');?></span>
    <span class="specimen_actions">
        <?php if($sf_user->isPinned($form->getObject()->getId(), 'specimen')) {
          $txt = image_tag('blue_pin_on.png', array('class'=>'pin_but pin_on'));
          $txt .= image_tag('blue_pin_off.png', array('class'=>'pin_but pin_off hidden'));
        }else{
          $txt = image_tag('blue_pin_on.png', array('class'=>'pin_but pin_on hidden'));
          $txt .= image_tag('blue_pin_off.png', array('class'=>'pin_but pin_off'));
        }?>

        <?php echo link_to($txt, 'savesearch/pin?source=specimen&id='.$form->getObject()->getId(), array('class'=>'pin_link'));?>
        <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))), 'specimen/view?id='.$form->getObject()->getId()); ?>
		<?php if($form->getObject()->getRestrictedAccess()):?>
			<i><b> Non public</b></i>
		<?php endif;?>
    </span>
  </h3>
<?php endif;?>

  <?php include_stylesheets_for_form($form) ?>
  <?php include_javascripts_for_form($form) ?>
  <?php use_javascript('double_list.js');?>
  <div>
  <!--new  control to catch error list of widgets RMCA 2018 02 13-->
    <div>
	  <?php $errors = $form->getErrorSchema()->getErrors() ?>
      <?php if($form->hasGlobalErrors()||count($errors)>0):?>
        <ul class="spec_error_list">
          <?php foreach ($form->getErrorSchema()->getErrors() as $name => $error): ?>
            <li class="error_fld_<?php echo $name;?>"><?php echo __($error) ?></li>
          <?php endforeach; ?>
		  <?php foreach( $errors as $name => $error ) : ?>
		     <li class="error_fld_<?php echo $name;?>"><?php echo $name ?> : <?php echo __($error) ?></li>
		  <?php endforeach ?>
		  <li>(Issue(s) might be caused by a closed widget containing a mandatory field) </li>
        </ul>
      <?php endif;?>
  </div>

  <?php 
		//RMCA 2015 10 19 (new identification, to redirect to a new action: split_created
		$formDisplay= form_tag('specimen/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()), array('class'=>'edition no_border','enctype'=>'multipart/form-data'));
		if(isset($newIdentification)&&isset($original_id))
		{
			if($newIdentification===TRUE&&isset($original_id))
			{
				$formDisplay=form_tag('specimen/'.($form->getObject()->isNew() ? 'create'.'?split_created='.$original_id : 'update?id='.$form->getObject()->getId()), array('class'=>'edition no_border','enctype'=>'multipart/form-data'));

			}
		}
        //ftheeten 2016 06 16
        elseif(isset($duplicateFlag))
        {
			if($duplicateFlag===TRUE&&isset($duplicate_id)&&$split_mode=="part")
			{
				$formDisplay=form_tag('specimen/'.($form->getObject()->isNew() ? 'create'.'?part_created='.$duplicate_id : 'update?id='.$form->getObject()->getId()), array('class'=>'edition no_border','enctype'=>'multipart/form-data'));
			}
            elseif($duplicateFlag===TRUE&&isset($duplicate_id)&&$split_mode=="duplicate")
            {
               $formDisplay=form_tag('specimen/'.($form->getObject()->isNew() ? 'create'.'?duplicate_created='.$duplicate_id : 'update?id='.$form->getObject()->getId()), array('class'=>'edition no_border','enctype'=>'multipart/form-data'));
            }
        }
		print($formDisplay);
	?>
	<?php if(!$form->getObject()->isNew()): ?>
		<?php if($user_rights_on_spec>=4): ?>
			<span class="specimen_actions">
				<?php print(__("Non public")); ?> : <?php print($form["restricted_access"]); ?>
			</span>
		<?php endif;?>
	<?php endif;?>	
    <?php echo $form['timestamp']->renderError(); ?>
    <?php echo $form['timestamp'];?>
    <div>
      <?php if($form->hasGlobalErrors()):?>
        <ul class="spec_error_list">
          <?php foreach ($form->getErrorSchema()->getErrors() as $name => $error): ?>
            <li class="error_fld_<?php echo $name;?>"><?php echo __($error) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif;?>

      <?php include_partial('widgets/screen', array(
        'widgets' => $widgets,
        'category' => 'specimenwidget',
        'columns' => 2,
        'options' => array('form' => $form, 'level' => 2),
      )); ?>
    </div>
    <p class="clear"></p>
   <?php include_partial('widgets/float_button', array('form' => $form,
	//ftheeten 2017 11 30
	 'module' => 'specimen')); ?>
    <p class="form_buttons">
      <?php if (!$form->getObject()->isNew()): ?>
        <?php echo link_to(__('New specimen'), 'specimen/new') ?>
        &nbsp;<a href="<?php echo url_for('specimen/new?duplicate_id='.$form->getObject()->getId());?>" class="duplicate_link"><?php echo __('Duplicate specimen');?></a>
		&nbsp;<a href="<?php echo url_for('specimen/new?part_id='.$form->getObject()->getId());?>" class="duplicate_link"><?php echo __('Split into parts');?></a>
		<!--RMCA 2015 10 19 new identifications-->
	    &nbsp;<a href="<?php echo url_for('specimen/new?split_id='.$form->getObject()->getId());?>" class="duplicate_link"><?php echo __('New identification');?></a>
        &nbsp;<?php echo link_to(__('Delete'), 'specimen/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => __('Are you sure?'))) ?>
      <?php endif?>
      &nbsp;<a href="<?php echo url_for('specimensearch/index') ?>" id="spec_cancel"><?php echo __('Cancel');?></a>
      <input type="submit" value="<?php echo __('Save');?>" id="submit_spec_f1"/>
    </p>
  </form>
<script  type="text/javascript">
 //ftheeten 2018 02 13
function addErrorToMain(html)
{

  $('ul#main_error_list').append(html);
  $('ul#main_error_list').show();
}

 //ftheeten 2018 02 13
function removeErrorFromMain()
{
  $('ul"main_error_list').hide();
  $('ul#main_error_list').find('li').text(' ');
}

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

$(document).ready(function () {
  $('body').duplicatable({duplicate_href: '<?php echo url_for('specimen/confirm');?>'});
  $('body').catalogue({});

  /*$('#submit_spec_f1').click(function(event){
	 //JMHerpers 2018/02/08	  
	if($('.mrac_input_mask').val() == null)
    {	
			alert ("Code is mandatory. Please fill the field");
			$('#add_code').focus();
			event.preventDefault();
	}		
	//rmca 2018 09 10
	else if($('.mrac_input_mask').val() == "")
    {	
			alert ("Code is mandatory and left empty. Please fill the field");
			$('#add_code').focus();
			event.preventDefault();
	}else{
		if($('#specimen_ig_ref_check').val() == 0 && $('#specimen_ig_ref').val() == "" && $('#specimen_ig_ref_name').val() != "")
		{
		  if(!window.confirm('<?php echo __("Your I.G. number will be lost ! are you sure you want continue ?") ; ?>'))
			event.preventDefault();
		}
	}
  }) ;  */
  
     //ftheeten 2015 10 14
  //to by pass unicity check has it is a duplicate
  <?php if(isset($newIdentification)): ?>
	<?php if($newIdentification===TRUE): ?>
		$(".class_unicity_check").attr('checked', false);
		$(".class_unicity_check_container").hide();
	<?php endif; ?>
 <?php endif; ?>
 
    //ftheeten 2018 02 13 (catch error messages of wiidgets and put them on top of page)
   var browseErrors=function() {
	   $(".error_list").not('.main_error_list').each(
			function(){
				var errorMsg=$(this).text();
				if(errorMsg.trim().length>0 && $(this).is(':visible'))	{
					addErrorToMain(errorMsg);								
				}
			}
	   );
   }
   
   browseErrors();
   
   <?php if(strpos($_SERVER['REQUEST_URI'],"/part_id")&&strpos($_SERVER['REQUEST_URI'],"/new")):?>
	var part_id=getUrlElem(window.location.href,"part_id");

	if(part_id!==undefined)
	{
		if(part_id.length>0)
		{ 
			$("#copy_code").click();
			$(".class_unicity_check").prop("checked", false);
			$('form.main_form').attr('action', $('form.main_form').attr('action') + '/part_id/'+part_id);
		}
	}
	<?php endif;?>
	$(".show_on_load").toggle();
	
});


</script>
</div></div>