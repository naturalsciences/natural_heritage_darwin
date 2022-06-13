<?php slot('title', __( $form->isNew() ? 'Add specimens' : 'Edit Specimen'));  ?>

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
  <h3 class="spec"><span class="title"><?php echo __( 'Add specimens');?></span></h3>
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
    </span>
  </h3>
<?php endif;?>

  <?php include_stylesheets_for_form($form) ?>
  <?php include_javascripts_for_form($form) ?>
  <?php use_javascript('double_list.js');?>
  <div>
  <!--new  control to catch error list of widgets RMCA 2018 02 13-->
    <div class="encod_screen edition" id="intro">
  <?php if($count>1):?>
    <div><ul style="background-color: #ec9593;border: 3px solid #c36b70;margin-bottom: 1em;padding: 1em"><?php print($count);?> Specimen with same collection number</ul></div>
  <?php endif;?> 
   <div>
    <ul id="main_error_list" class="error_list main_error_list" style="display:none">
    <ul id="error_list" class="error_list" style="display:none">
      <li></li>
    </ul>
  </div>

    <?php
	$formDisplay= form_tag('specimen/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()), array('class'=>'edition no_border main_form','enctype'=>'multipart/form-data'));
	if(isset($partFlag))
	{
		if($partFlag&& isset($part_id))
		{
			$formDisplay= form_tag('specimen/'.($form->getObject()->isNew() ? 'create?part_id='.$part_id : 'update?id='.$form->getObject()->getId()."&part_id=".$part_id), array('class'=>'edition no_border main_form','enctype'=>'multipart/form-data'));
		}
	}
	print($formDisplay);
	?>
    <div>
	  <?php $errors = $form->getErrorSchema()->getErrors() ?>
      <?php if($form->hasGlobalErrors()||count($errors)>0):?>
        <ul class="spec_error_list">
          <?php foreach ($form->getErrorSchema()->getErrors() as $name => $error): ?>
            <li class="error_fld_<?php echo $name;?>"><?php echo __($error) ?></li>
          <?php endforeach; ?>
		  <?php foreach( $errors as $name => $error ) : ?>
		     <li class="error_fld_<?php echo $name;?>"><?php echo $name ?> : <?php echo __($error) ?></li>
		  <?php endforeach; ?>
		  <li>(Issue(s) might be caused by a closed widget containing a mandatory field) </li>
        </ul>
      <?php endif;?>		
		<?php $form->isNew() ? $url_tpl="specimen/new" : $url_tpl=sfContext::getInstance()->getRequest()->getUri(); ?>
		<?php  $count_templates=Doctrine_Core::getTable('Users')->getWidgetTemplates($sf_user->getId(), true); ?>
		<?php if(count($count_templates)>1): ?>
			<div class="dw_anchor_button">
			<div>
				<div><?php print($form["widget_template"]) ?></div>
				<div style="margin-top:5px"><p class="form_buttons"><?php echo link_to(__("change template"),$url_tpl, array("id"=>"link_change_template"));?></p></div>
			</div>
			</div>
		<br/>
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
                                                        'module' => 'specimen',
                                                        'search_module'=>'specimensearch/index',
                                                        'save_button_id' => 'submit_spec_f1')
    ); ?>
	<p>
	<?php if($duplic>0):?>
	<table>
	<tr>
		<td>
			<h2><?php echo __('Do you also want to duplicate properties');?></h2>
		</td>
		<td>
			Yes : <input type="radio" name="copy_properties" value="yes" > No : <input type="radio" name="copy_properties" value="no"  checked="checked">
		</td>
	</tr>
  </table>
	<?php endif;?>
	</p>
    <p class="form_buttons">
      <?php if (!$form->getObject()->isNew()): ?>
        <?php echo link_to(__('New specimen'), 'specimen/new') ?>
        &nbsp;<a target="_self" href="<?php echo url_for('specimen/new?duplicate_id='.$form->getObject()->getId());?>" class="duplicate_link"><?php echo __('Duplicate specimen');?></a>
		<a href="<?php echo url_for('specimen/new?duplicate_id='.$form->getObject()->getId().'&part_id='.$form->getObject()->getId());?>" class="duplicate_link"><?php echo __('Split into parts');?></a>
        &nbsp;<?php echo link_to(__('Delete'), 'specimen/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => __('Are you sure?'))) ?>
      <?php endif?>
      &nbsp;<a href="<?php echo url_for('specimensearch/index') ?>" id="spec_cancel"><?php echo __('Cancel');?></a>
      <input type="submit" value="<?php echo __('Save');?>" id="submit_spec_f1"/>
    </p>
	<?php if($duplic>0):?>
		<input type="hidden" name="duplicate_id" id="duplicate_id" value="<?php print($duplic);?>"/>
		<input type="hidden" name="keep_duplicate" id="keep_duplicate" value="off"/>
	<?php endif;?>
  </form>
<script  type="text/javascript">


	var template_url="";
 


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
  $('body').duplicatable({duplicate_href: '<?php echo url_for('specimen/confirm');?>', target:'_self'});
  $('body').catalogue({});

 <?php if(strpos($_SERVER['REQUEST_URI'],"/create")):?>
var part_id=getUrlElem(window.location.href,"part_id");

if(part_id!==undefined)
{
	if(part_id.length>0)
	{ 
		$('form.main_form').attr('action', $('form.main_form').attr('action') + '/part_id/'+part_id);
	}
}
var duplicate_id=getUrlElem(window.location.href,"duplicate_id");

if(duplicate_id!==undefined)
{
	if(duplicate_id.length>0)
	{
		$('form.main_form').attr('action', $('form.main_form').attr('action') + '/duplicate_id/'+duplicate_id);
	}
}
<?php endif; ?>

  $('#submit_spec_f1').click(function(event){
	 //JMHerpers 2018/02/08	  

	if($('.code_mrac_input_mask').val() == null)
    {	
			alert ("Code is mandatory. Please fill the field");
			$('#add_code').focus();
			event.preventDefault();
	}		
	//rmca 2018 09 10
	else if($('.code_mrac_input_mask').val() == "")
    {	
			alert ("Code is mandatory and left empty. Please fill the field");
			$('#add_code').focus();
			event.preventDefault();
	}else{
		
		
		if($('#specimen_ig_ref_check').val() == 0 && $('#specimen_ig_ref').val() == "" && $('#specimen_ig_ref_name').val() != "")
		{
		  if(!window.confirm('Your I.G. number will be lost ! are you sure you want continue ?'))
			event.preventDefault();
		}
		<?php if($duplic>0):?>
		 //if(window.confirm('Do you want to keep trace of the duplicate relation in the database ?'))
		 //{
			 $("#keep_duplicate").val("on");
		 //}
		<?php endif;?>
	}
  }) ; 
  
  //ftheeten 2015 10 14
  //to by pass unicity check has it is a duplicate
  <?php if(isset($newIdentification)): ?>
	<?php if($newIdentification===TRUE): ?>
		$(".class_unicity_check").attr('checked', false);
		$(".class_unicity_check_container").hide();
	<?php endif; ?>
 <?php endif; ?>
 
    //ftheeten 2018 02 13 (catch error messages of wiidgets and put them on top of page)
   var browseErrors=function()
   {
	  
	   $(".error_list").not('.main_error_list').each(
			function()
			{
							var errorMsg=$(this).text();
							if(errorMsg.trim().length>0 && $(this).is(':visible'))
							{
							
								addErrorToMain(errorMsg);								
							}
			}
	   );
       if ($('.main_error_list')[0]){
            if($('.main_error_list').css('display').toLowerCase()!="none")
            {
                 alert("Error, data not recorded in database ! Check submitted data");
            }
        }
	   
   }

   template_url=$("#link_change_template").attr('href');
   $("#specimen_widget_template").on('change', function (e) {	
	   var valueSelected = this.value;
	   console.log(valueSelected);
	   if(valueSelected.length>0)
	   {
			var template_url_local=template_url;			
			template_url_local=template_url_local.replace(/\/widget_template\/\-?\d+/g,"");
			console.log(template_url_local);
			var href = template_url_local+"/widget_template/"+valueSelected;
			console.log(href);
			$("#link_change_template").attr('href', href);
	   }
		
   });
   
   <?php if($widget_template!="-1"):?>
        console.log("TEMPLATE_DEF");
		console.log("<?php print($debug);?>");
		$("#specimen_widget_template").val("<?php print($widget_template);?>");
   <?php endif;?>
   
   browseErrors();
});
</script>
</div></div>