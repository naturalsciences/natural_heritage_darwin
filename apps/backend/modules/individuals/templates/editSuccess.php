<?php slot('title', __( $individual->getObject()->isNew() ? 'Add specimen individual' : 'Edit specimen individual'));  ?>

<?php include_partial('widgets/list', array('widgets' => $widgets, 'category' => 'individuals', 'table' => 'specimen_individuals', 'eid'=> ($individual->getObject()->isNew() ?null: $individual->getObject()->getId()))); ?>
<?php include_partial('specimen/specBeforeTab', array('specimen' => $specimen, 'individual'=> $individual->getObject(), 'mode'=>'individual_edit'));?>

<?php include_stylesheets_for_form($individual) ?>
<?php include_javascripts_for_form($individual) ?>

<div>
<ul id="error_list" class="error_list" style="display:none">
  <li></li>
</ul>
</div>

<?php echo form_tag('individuals/edit'. ($individual->isNew() ? '?spec_id='.$specimen->getId() : '?id='.$individual->getObject()->getId()), array('enctype'=>'multipart/form-data') );?>
  <div>
    <?php echo $individual['specimen_ref']->render(); ?>
    <?php if($individual->hasGlobalErrors()):?>
      <ul class="spec_error_list">
      <?php foreach ($individual->getErrorSchema()->getErrors() as $name => $error): ?>
        <li class="error_fld_<?php echo $name;?>"><?php echo __($name." ".$error) ?></li>
      <?php endforeach; ?>
      </ul>
    <?php endif;?>

    <?php include_partial('widgets/screen', array(
        'widgets' => $widgets,
        'category' => 'individualswidget',
        'columns' => 2,
        'options' => array('form' => $individual),
      )); ?>

  </div>
  <p class="clear"></p>
  <p class="form_buttons">
    <?php if (!$individual->getObject()->isNew()): ?>
      <?php echo link_to(__('New individual'), 'individuals/edit?spec_id='.$specimen->getId()) ?>
      &nbsp;<?php echo link_to(__('Duplicate individual'), 'individuals/edit?spec_id='.$individual->getObject()->getSpecimenRef().
      '&duplicate_id='.$individual->getObject()->getId(),array('class' => 'duplicate_link')) ?>
        &nbsp;<?php echo link_to(__('Delete'), 'individuals/delete?id='.$individual->getObject()->getId(), array('method' => 'delete', 'confirm' => __('Are you sure?'))) ?>
    <?php endif?>
    &nbsp;<a href="<?php echo url_for('individuals/overview?spec_id='.$specimen->getId()) ?>"><?php echo __('Cancel');?></a>
    <input type="submit" value="<?php echo __('Save');?>" id="submit_spec_individual_f1"/>
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
  check_screen_size() ;
  $(window).resize(function(){
    check_screen_size();
  });   
  $('body').duplicatable({duplicate_href: '<?php echo url_for('specimen/confirm');?>'});
  $('body').catalogue({});
  $("a#spec_ind_delete").click(function(){
     if(confirm($(this).attr('title')))
     {
       currentElement = $(this);
       removeError();
       $.ajax({
               url: $(this).attr('href'),
               success: function(html) {
		      if(html == "ok" )
		      {
			// Reload page
			$(location).attr('href',$('a#tab_1').attr('href'));
		      }
		      else
		      {
			addError(html); //@TODO:change this!
		      }
		},
               error: function(xhr){
		  addError('Error!  Status = ' + xhr.status);
               }
             }
            );
    }
    return false;
  });
});
</script>

<?php include_partial('specimen/specAfterTab');?>
