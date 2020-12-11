<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<script type="text/javascript">
$(document).ready(function () 
{
  $('body').catalogue({});
});
</script>

<?php echo form_tag('bibliography/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()), array('class'=>'edition'));?>

<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['type']->renderLabel() ?></th>
        <td>
          <?php echo $form['type']->renderError() ?>
          <?php echo $form['type'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['title']->renderLabel() ?></th>
        <td>
          <?php echo $form['title']->renderError() ?>
          <?php echo $form['title'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['year']->renderLabel() ?></th>
        <td>
          <?php echo $form['year']->renderError() ?>
          <?php echo $form['year'] ?>
          <div class="small_info"><?php echo __('(The year of publication (or, if unpublished, the year of creation) on 4 digits)');?></div>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['abstract']->renderLabel() ?></th>
        <td>
          <?php echo $form['abstract']->renderError() ?>
          <?php echo $form['abstract'] ?>
        </td>
      </tr>
	  <tr>
        <th><?php echo $form['reference']->renderLabel() ?></th>
        <td>
          <?php echo $form['reference']->renderError() ?>
          <?php echo $form['reference'] ?>
        </td>
      </tr>
	   <tr>
        <th><?php echo $form['uri_protocol']->renderLabel() ?></th>
        <td>
          <?php echo $form['uri_protocol']->renderError() ?>
          <?php echo $form['uri_protocol'] ?>
        </td>
      </tr>
	   <tr>
        <th><?php echo $form['uri']->renderLabel() ?></th>
        <td>
          <?php echo $form['uri']->renderError() ?>
          <?php echo $form['uri'] ?>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <?php echo $form['Authors_holder'];?>
          <table class="encoding collections_rights" id="bib_author_table">
            <thead>
              <tr>
                <th>&nbsp;</th>
                <th><label><?php echo __("Authors") ; ?></label></th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <tbody>
                <?php $retainedKey = 0;?>
              <?php foreach($form['Authors'] as $form_value):?>
                <?php include_partial('author_row', array('form' => $form_value, 'ref_id' => ($form->getObject()->isNew() ? '':$form->getObject()->getId()), 'row_num'=>$retainedKey));
        $retainedKey++;?>
              <?php endforeach;?>
              <?php foreach($form['newAuthors'] as $form_value):?>
                <?php include_partial('author_row', array('form' => $form_value, 'ref_id' => ($form->getObject()->isNew() ? '':$form->getObject()->getId()), 'row_num'=>$retainedKey));
        $retainedKey++;?>
              <?php endforeach;?>
            </tbody>
          </table>
          <div class='add_value'>
            <a href="<?php echo url_for('bibliography/addAuthor'. ($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>/num/" class="hidden"></a>
            <a class='add_author' href="<?php echo url_for('people/choose?with_js=1');?>"><?php echo __('Add Author');?></a>
          </div>
        </td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form['id'] ?>

          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('New bibliography'), 'bibliography/new') ?>
            &nbsp;<?php echo link_to(__('Duplicate bibliography'), 'bibliography/new?duplicate_id='.$form->getObject()->getId()) ?>
          <?php endif?>

          &nbsp;<a href="<?php echo url_for('bibliography/index') ?>"><?php echo __('Cancel');?></a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to(__('Delete'), 'bibliography/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => __('Are you sure?'))) ?>
          <?php endif; ?>
          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>

<?php echo javascript_include_tag('catalogue_people.js') ?>

<script  type="text/javascript">
$(document).ready(function () {


function addAuthor(people_ref, people_name)
{ 

  info = 'ok';
  $('#bib_author_table tbody tr').each(function() {
    if($(this).find('input[id$=\"_people_ref\"]').val() == people_ref) info = 'bad' ;
  });
  if(info != 'ok') return false;
  hideForRefresh($('.ui-tooltip-content .page')) ; 
  $.ajax({
    type: "GET",
    url: $('.add_value a.hidden').attr('href')+ (0+$('#bib_author_table tbody tr').length)+'/people_ref/'+people_ref + '/iorder_by/' + (0+$('#bib_author_table tbody tr').length),
    success: function(html)
    {
      $('#bib_author_table tbody').append(html);
      $.fn.catalogue_people.reorder($('#bib_author_table'));
      showAfterRefresh($('.ui-tooltip-content .page')) ; 
	   //ftheeten 2020 12 11
		try 
		{
		  $('body').trigger('close_modal');
		}
		catch(err) 
		{
		  
		}
    }
  });
  return true;
}


$("#bib_author_table").catalogue_people({add_button: 'a.add_author',update_row_fct: addAuthor,q_tip_text : '<?php echo __("Choose an Author") ;?>' });


});

</script>
