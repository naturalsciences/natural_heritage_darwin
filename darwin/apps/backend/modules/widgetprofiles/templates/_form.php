<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php echo form_tag('widgetprofiles/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()), array('class'=>'edition'));?>

 <?php echo $form['duplicate'] ?>
<?php echo $form->renderGlobalErrors() ?>
<table class="collections">
    <tbody>
      <tr>
        <th>
	  <?php echo $form['name']->renderLabel("Name") ?>
          <?php echo help_ico($form['name']->renderHelp(),$sf_user);?>
        </th>
        <td>
          <?php echo $form['name']->renderError() ?>
          <?php echo $form['name'] ?>
        </td>
      </tr>
	  <tr>
        <th><?php echo $form['creator_ref']->renderLabel() ?></th>
        <td>
          <?php echo $form['creator_ref']->renderError() ?>
          <?php echo $form['creator_ref'] ?>
        </td>
      </tr>
	  <tr>
        <th><?php echo $form['creation_date']->renderLabel() ?></th>
        <td>
          <?php echo $form['creation_date']->renderError() ?>
          <?php echo $form['creation_date'] ?>
        </td>
      </tr>
	 </body>
	 <tfoot>
	 <tr>
        <td colspan="2">
          <?php echo $form['id'] ?>
          <?php if (!$form->getObject()->isNew()): ?>
      	    <a href="<?php echo url_for('widgetprofiles/new') ?>"><?php echo __('New profile');?></a>
      	    &nbsp;<?php echo link_to(__('Duplicate profiles'), 'widgetprofiles/new?duplicate_id='.$form->getObject()->getId()) ?>
            &nbsp;<?php echo link_to(__('Delete'), 'widgetprofiles/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => __('Are you sure?'))) ?>
          <?php endif; ?>
          &nbsp;<a href="<?php echo url_for('widgetprofiles/index') ?>"><?php echo __('Cancel');?></a>
          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
	 </tfoot>
</table>
<?php if (!$form->getObject()->isNew()): ?>
<table class="collections">
    <tbody>
      <tr>
	  <td>
		<div> <p class="form_buttons"><?php echo link_to(__('Edit widgets'), 'widgetprofiles/editwidgets?id='.$form->getObject()->getId(), array("target"=> "_blank")) ?></p></div>
	  </td>
	  </tr>
	 </tbody>
</table>
<?php endif;  ?>

<script language="JavaScript">
	$(document).ready(
		function()
		{
			var tmp="<?php print($duplic);?>";
			//console.log(tmp);
			if(tmp!="0")
			{
				$(".duplicate").val(tmp);
			}
		}
	)
</script>

</form>
