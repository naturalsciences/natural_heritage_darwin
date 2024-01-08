<?php use_javascript('button_ref.js') ?>
<?php slot('title','Mass Actions');?>

<div class="page" id="mass_action">
  <h1><?php echo __('Mass Actions :');?></h1>
  <?php $errors = $form->getErrorSchema()->getErrors() ?>
      <?php if($form->hasGlobalErrors()||count($errors)>0):?>
        <ul class="spec_error_list">
          <?php foreach ($form->getErrorSchema()->getErrors() as $name => $error): ?>
            <li class="error_fld_<?php echo $name;?>"><?php echo __($error) ?></li>
          <?php endforeach; ?>
		  <?php foreach( $errors as $name => $error ) : ?>
		     <li class="error_fld_<?php echo $name;?>"><?php echo $name ?> : <?php echo __($error) ?></li>
		  <?php endforeach ?>		  
        </ul>
      <?php endif;?>
  <?php echo form_tag('massactions/index', array('autocomplete'=>"off"));?>
  <table>
    <tbody>
      <tr>
        <td colspan="2">
          <div  id="item_list">
            <?php if(isset($items)):?>
              <?php include_partial('itemlist',array('items'=>$items));?>
            <?php else:?>
              <?php echo $form['item_list'];?>
            <?php endif;?>
          </div>
        </td>
      </tr>

      <tr>
        <th><?php echo $form['field_action']->renderLabel();?></th>
        <td class="fld_group"><?php echo $form['field_action'];?></td>
      </tr>

      <tr id="action_sub_form">
        <td colspan="2" >
          <div>
            <?php foreach($form['MassActionForm'] as  $key => $sform):?>
              <?php include_partial('subform',array('form'=>$form,'mAction' => $key));?>
            <?php endforeach;?>
          </div>
        </td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2"><input type="submit" id="mass_submit" value="<?php echo __('Go');?>" /></td>
      </tr>
    </tfoot>
  </table>
  </form>
</div>
<script type="text/javascript">

function chooseAction(event)
{
  if(!event) return; 
  if(! $(this).is(':checked'))
  {
    $('#sub_form_'+$(this).val()).remove();
    if( $('#mass_action .fld_group ul :checked').length == 0)
    {
      $('#action_sub_form').addClass('disabled');
      changeSubmit(false);
    }
  }
  else
  {
    $('#action_sub_form').removeClass('disabled');
    $("#action_sub_form > td > div").append('<?php echo image_tag('loader.gif',array('class'=>'loading_img'));?>');
    $.get('<?php echo url_for('massactions/getSubForm');?>/source/' + $('#mass_action_source').val() + '/maction/' + $(this).val() ,
      function(data){
        $('.loading_img').remove();
        $('#action_sub_form > td > div').append(data);
      });
    changeSubmit(false);
  }
}

function changeSubmit(status)
{
  if(status)
    $('#mass_submit').removeAttr('disabled');
  else
    $('#mass_submit').attr('disabled','disabled');
}

$(document).ready(function () {

  $('#mass_action .fld_group ul :checkbox').change(chooseAction);
  $('#mass_submit').closest('form').submit(function (event)
  {
    if(! confirm('<?php echo addslashes(__('Are you sure ?')) ?>'))
    {
      event.preventDefault();
    }
  });

});

</script>
