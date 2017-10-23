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
      <?php endif ?>
      <?php if($type == 'taxon') : ?>
      <tr>
        <th><?php echo $form['exclude_invalid_entries']->renderLabel() ?> :</th>
        <td>
          <?php echo $form['exclude_invalid_entries']->renderError() ?>
          <?php echo $form['exclude_invalid_entries'] ?>
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
