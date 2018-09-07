<?php include_javascripts_for_form($form) ?>
<div id="address_screen">
<?php echo form_tag('user/address?ref_id='.$sf_request->getParameter('ref_id') . ($form->getObject()->isNew() ? '': '&id='.$form->getObject()->getId() ), array('class'=>'edition qtiped_form','id'=>'address_form'));?>
<?php echo $form['person_user_ref'];?>
<table>
  <tbody>
    <tr>
        <td colspan="2">
          <?php echo $form->renderGlobalErrors() ?>
        </td>
    </tr>
    <tr>
      <th><?php echo $form['organization_unit']->renderLabel('Organization');?></th>
      <td>
        <?php echo $form['organization_unit']->renderError(); ?>
        <?php echo $form['organization_unit'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['person_user_role']->renderLabel('Role in the organization');?></th>
      <td>
        <?php echo $form['person_user_role']->renderError(); ?>
        <?php echo $form['person_user_role'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['entry']->renderLabel('Street');?></th>
      <td>
        <?php echo $form['entry']->renderError(); ?>
        <?php echo $form['entry'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['extended_address']->renderLabel();?></th>
      <td>
        <?php echo $form['extended_address']->renderError(); ?>
        <?php echo $form['extended_address'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['po_box']->renderLabel();?></th>
      <td>
        <?php echo $form['po_box']->renderError(); ?>
        <?php echo $form['po_box'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['zip_code']->renderLabel();?></th>
      <td>
        <?php echo $form['zip_code']->renderError(); ?>
        <?php echo $form['zip_code'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['locality']->renderLabel();?></th>
      <td>
        <?php echo $form['locality']->renderError(); ?>
        <?php echo $form['locality'];?>
      </td>
    </tr>
    <tr>
      <th><?php echo $form['region']->renderLabel();?></th>
      <td>
        <?php echo $form['region']->renderError(); ?>
        <?php echo $form['region'];?>
      </td>
    </tr>
    <tr>
      <th class="top_aligned"><?php echo $form['country']->renderLabel();?></th>
      <td>
        <?php echo $form['country']->renderError(); ?>
        <?php echo $form['country'];?>
      </td>
    </tr>
    <tr>
      <th class="top_aligned"><?php echo $form['tag']->renderLabel();?></th>
      <td>
        <?php echo $form['tag']->renderError(); ?>
        <?php echo $form['tag'];?>
      </td>
    </tr>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="2">
        <a href="#" class="cancel_qtip"><?php echo __('Cancel');?></a>
        <?php if(! $form->getObject()->isNew()):?>
          <?php echo link_to(__('Delete'),'catalogue/deleteRelated?table=users_addresses&id='.$form->getObject()->getId(),array('class'=>'delete_button','title'=>__('Are you sure ?')));?>
        <?php endif;?> 
        <input id="submit" type="submit" value="<?php echo __('Save');?>" />
      </td>
    </tr>
  </tfoot>
</table>
<script  type="text/javascript">
$(document).ready(function () {
    $('form.qtiped_form').modal_screen();
});
</script>
</form>

</div>
