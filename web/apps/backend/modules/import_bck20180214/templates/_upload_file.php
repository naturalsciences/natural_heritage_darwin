<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<script type="text/javascript">
$(document).ready(function () 
{   
  $('#uploadfield').bind('change', function() {
    $('input#input_text_file').val($(this).val());
  });
});
</script>
<?php echo form_tag('import/upload', array('class'=>'edition','enctype'=>'multipart/form-data','method'=>'post'));?>

<div class="container">
  <table class="search">
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['uploadfield']->renderLabel() ?> :</th>
        <td>
          <?php echo $form['uploadfield']->renderError() ?>
          <div class="containerFile">
            <div class="divFile">
              <input id="input_text_file" class="inputText"
              readonly="readonly"/>
              <?php echo $form['uploadfield']; ?>
              <?php echo image_tag('slide_right_enable_new.png') ; ?>
            </div>
          </div>        
        </td>
      </tr>
      <tr>
        <th><?php echo $form['format']->renderLabel() ?> :</th>
        <td>
          <?php echo $form['format']->renderError() ?>
          <?php echo $form['format'] ?>
        </td>
      </tr>
      <?php if($type != 'taxon') : ?>
      <tr>
        <th><?php echo $form['collection_ref']->renderLabel() ?> :</th>
        <td>
          <?php echo $form['collection_ref']->renderError() ?>
          <?php echo $form['collection_ref'] ?>
        </td>
      </tr>
      <!--ftheeten 2017 08 02-->
      <tr>
        <th><?php echo $form['specimen_taxonomy_ref']->renderLabel() ?> :</th>
        <td>
          <?php echo $form['specimen_taxonomy_ref']->renderError() ?>
          <?php echo $form['specimen_taxonomy_ref'] ?>
        </td>
      </tr>
      <?php endif ?>
      <?php if($type == 'taxon') : ?>
      <tr>
        <th><?php echo $form['exclude_invalid_entries']->renderLabel() ?> :</th>
        <td>
          <?php echo $form['exclude_invalid_entries']->renderError() ?>
          <?php echo $form['exclude_invalid_entries'] ?>
        </td>        
      </tr>
      <!--ftheeten 2017 07 06-->
      <tr>
        <th><?php echo $form['taxonomy_name']->renderLabel() ?> :</th>
        <td>
          <?php echo $form['taxonomy_name']->renderError() ?>
          <?php echo $form['taxonomy_name'] ?>
        </td>        
      </tr>
      <tr>
        <th><?php echo $form['creation_date']->renderLabel() ?> :</th>
        <td>
          <?php echo $form['creation_date']->renderError() ?>
          <?php echo $form['creation_date'] ?>
        </td>        
      </tr>
      <tr>
        <th><?php echo $form['is_reference_taxonomy']->renderLabel() ?> :</th>
        <td>
          <?php echo $form['is_reference_taxonomy']->renderError() ?>
          <?php echo $form['is_reference_taxonomy'] ?>
        </td>        
      </tr>
      <tr>
        <th><?php echo $form['source_taxonomy']->renderLabel() ?> :</th>
        <td>
          <?php echo $form['source_taxonomy']->renderError() ?>
          <?php echo $form['source_taxonomy'] ?>
        </td>        
      </tr>
      <tr>
        <th><?php echo $form['definition_taxonomy']->renderLabel() ?> :</th>
        <td>
          <?php echo $form['definition_taxonomy']->renderError() ?>
          <?php echo $form['definition_taxonomy'] ?>
        </td>        
      </tr>
       <tr>
        <th><?php echo $form['url_website_taxonomy']->renderLabel() ?> :</th>
        <td>
          <?php echo $form['url_website_taxonomy']->renderError() ?>
          <?php echo $form['url_website_taxonomy'] ?>
        </td>        
      </tr>
      <tr>
        <th><?php echo $form['url_webservice_taxonomy']->renderLabel() ?> :</th>
        <td>
          <?php echo $form['url_webservice_taxonomy']->renderError() ?>
          <?php echo $form['url_webservice_taxonomy'] ?>
        </td>        
      </tr>
      <?php endif ?>
    </tbody>
      <tfoot>
        <tr>
          <td colspan="2">
            <input id="submit" type="submit" value="<?php echo __('Submit');?>" />
          </td>
        </tr>
      </tfoot>  
  </table>
</div>
