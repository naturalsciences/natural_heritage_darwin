<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<?php echo form_tag('taxonomy/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()), array('class'=>'edition', 'enctype'=>'multipart/form-data'));?>

<?php include_partial('catalogue/commonJs');?>

  <table class="classifications_edit">
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['name']->renderLabel() ?></th>
        <td>
          <?php echo $form['name']->renderError() ?>
          <?php echo $form['name'] ?>
        </td>
      </tr>
	  <tr>
	    <!-- jmherpers 2018 03 08-->
        <th><?php echo __('Taxonomy');?></th>
        <td>
          <?php echo $form['metadata_ref']->renderError() ?>
          <?php echo $form['metadata_ref'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['level_ref']->renderLabel() ?></th>
        <td>
          <?php echo $form['level_ref']->renderError() ?>
          <?php echo $form['level_ref'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['status']->renderLabel() ?></th>
        <td>
          <?php echo $form['status']->renderError() ?>
          <?php echo $form['status'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['extinct']->renderLabel() ?></th>
        <td>
          <?php echo $form['extinct']->renderError() ?>
          <?php echo $form['extinct'] ?>
        </td>
      </tr>
      
        <!-- ftheeten 2017 07 03-->
      <tr>
	    <!-- jmherpers 2018 03 08-->
        <th><?php echo __('Collection');?></th>
        <td>
          <?php echo $form['collection_ref']->renderError() ?>
          <?php echo $form['collection_ref'] ?>
        </td>
      </tr>
      <!-- ftheeten 2017 07 03-->
      <tr>
        <th><?php echo $form['sensitive_info_withheld']->renderLabel() ?></th>
        <td>
          <?php echo $form['sensitive_info_withheld']->renderError() ?>
          <?php echo $form['sensitive_info_withheld'] ?>
        </td>
      </tr>
        <!-- ftheeten 2017 07 03-->
      <tr id="parent_ref">
	    <!-- jmherpers 2018 03 08-->
        <th><?php echo __('Parent taxon');?></th>
        <td>
          <?php echo $form['parent_ref']->renderError() ?>
          <?php echo $form['parent_ref'] ?>
          <div class="warn_message ref_name button hidden" id="taxonomy_parent_ref_warning"><?php echo __('The parenty does not follow the possible upper level rule');?></div>
        </td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('New Taxa'), 'taxonomy/new') ?>
            &nbsp;<?php echo link_to(__('Duplicate Taxa'), 'taxonomy/new?duplicate_id='.$form->getObject()->getId()) ?>
          <?php endif?>

          <?php echo $form['id']->render() ?><?php echo $form['table']->render() ?><?php echo link_to('search PUL', 'catalogue/searchPUL', array('id' => 'searchPUL', 'class' => 'hidden'));?>&nbsp;<a href="<?php echo url_for('taxonomy/index') ?>"><?php echo __('Cancel');?></a>

          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to(__('Delete'), 'taxonomy/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => __('Are you sure?'))) ?>
          <?php endif; ?>

          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>
<?php use_stylesheet('ui.datepicker.css'); ?>
<script type="text/javascript">

$(document).ready(function () {
  <?php if($form->hasErrors())
      echo "$('tr#parent_ref .button').show();";
  ?>

  <?php if($form['level_ref']->getValue())
      echo "$('#taxonomy_level_ref').trigger('change');" ;
  ?>
  $('#taxonomy_name').autocomplete({
      minLength: 3,
      source: function( request, response ) {
        $.getJSON('<?php echo url_for('catalogue/completeName?table=taxonomy');?>', {term : request.term }, function( data) {
            response( $.map( data, function( item ) {
              return {
                label: item.label,
                value: item.label
              }
            }));

        });
      }
    });
    //ftheeten 2017 07 06
    <?php if($collection_ref_for_insertion>-1):?>
         $('.coll_for_taxonomy_insertion_ref option[value="<?php print($collection_ref_for_insertion);?>"]').attr("selected", true);
     <?php endif?>
     
   //ftheeten 2018 03 14
   $('form').submit(
        function()
        {
            
            var referrer=document.referrer;
            var tmpName=$(".taxonomy_name_callback").val();
            localStorage.setItem("last_scientific_name", tmpName);
           
        }
   ); 

    //ftheeten 2018 03 14
	<?php if($form->getObject()->isNew()===true): ?>
    if ( $( ".col_check_metadata_ref" ).length ) {
     
        $(".col_check_metadata_ref option:eq(1)").attr("selected", "selected");
     
    }   
	 <?php endif?>
});
</script>
