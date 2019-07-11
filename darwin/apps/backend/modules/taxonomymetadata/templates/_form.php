<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<!--ftheeten 2017 07 17-->
<?php echo form_tag('taxonomymetadata/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()), array('class'=>'edition', 'enctype'=>'multipart/form-data'));?>

<?php include_partial('catalogue/commonJs');?>

  <table class="classifications_edit">
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['taxonomy_name']->renderLabel() ?></th>
        <td>
          <?php echo $form['taxonomy_name']->renderError() ?>
          <?php echo $form['taxonomy_name'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['definition']->renderLabel() ?></th>
        <td>
          <?php echo $form['definition']->renderError() ?>
          <?php echo $form['definition'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['is_reference_taxonomy']->renderLabel() ?></th>
        <td>
          <?php echo $form['is_reference_taxonomy']->renderError() ?>
          <?php echo $form['is_reference_taxonomy'] ?>
        </td>
      </tr>
       <tr>
        <th><?php echo $form['creation_date']->renderLabel() ?></th>
        <td>
          <?php echo $form['creation_date']->renderError() ?>
          <?php echo $form['creation_date'] ?>
        </td>
      </tr>
       <tr>
        <th><?php echo $form['source']->renderLabel() ?></th>
        <td>
          <?php echo $form['source']->renderError() ?>
          <?php echo $form['source'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['url_website']->renderLabel() ?></th>
        <td>
          <?php echo $form['url_website']->renderError() ?>
          <?php echo $form['url_website'] ?>
        </td>
      </tr>
       <tr>
        <th><?php echo $form['url_webservice']->renderLabel() ?></th>
        <td>
          <?php echo $form['url_webservice']->renderError() ?>
          <?php echo $form['url_webservice'] ?>
        </td>
      </tr>
     </tbody>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('New Taxonomic metadata'), 'taxonomymetadata/new') ?>
            &nbsp;<?php echo link_to(__('Duplicate Taxonomy'), 'taxonomymetadata/new?duplicate_id='.$form->getObject()->getId()) ?>
          <?php endif?>

          <?php echo $form['id']->render() ?><?php echo $form['table']->render() ?><?php echo link_to('search PUL', 'catalogue/searchPUL', array('id' => 'searchPUL', 'class' => 'hidden'));?>&nbsp;<a href="<?php echo url_for('taxonomymetadata/index') ?>"><?php echo __('Cancel');?></a>

          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to(__('Delete'), 'taxonomymetadata/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => __('Are you sure?'))) ?>
          <?php endif; ?>

          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
    </tfoot>     
    </table>
</form>   