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
    <ul id="error_list" class="error_list" style="display:none">
      <li></li>
    </ul>
  </div>

  <?php echo form_tag('specimen/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()), array('class'=>'edition no_border','enctype'=>'multipart/form-data'));?>
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
                                                        'module' => 'specimen',
                                                        'search_module'=>'specimensearch/index',
                                                        'save_button_id' => 'submit_spec_f1')
    ); ?>
    <p class="form_buttons">
      <?php if (!$form->getObject()->isNew()): ?>
        <?php echo link_to(__('New specimen'), 'specimen/new') ?>
        &nbsp;<a href="<?php echo url_for('specimen/new?duplicate_id='.$form->getObject()->getId());?>" class="duplicate_link"><?php echo __('Duplicate specimen');?></a>
        &nbsp;<?php echo link_to(__('Delete'), 'specimen/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => __('Are you sure?'))) ?>
      <?php endif?>
      &nbsp;<a href="<?php echo url_for('specimensearch/index') ?>" id="spec_cancel"><?php echo __('Cancel');?></a>
      <input type="submit" value="<?php echo __('Save');?>" id="submit_spec_f1"/>
    </p>
  </form>
<script  type="text/javascript">

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

  $('#submit_spec_f1').click(function(event){
    if($('#specimen_ig_ref_check').val() == 0 && $('#specimen_ig_ref').val() == "" && $('#specimen_ig_ref_name').val() != "")
    {
      if(!window.confirm('<?php echo __("Your I.G. number will be lost ! are you sure you want continue ?") ; ?>'))
        event.preventDefault();
    }
  }) ;
});
</script>
</div></div>
