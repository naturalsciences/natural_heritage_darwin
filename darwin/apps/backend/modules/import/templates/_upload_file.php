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
	  <!--ftheeten 2018 08 05-->
	   <tr>
        <th><?php echo $form['source_database']->renderLabel(); ?> :</th>
        <td><?php echo $form['source_database']; ?></td>
      </tr>
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
      <?php if($type != 'taxon'&&$type != 'lithostratigraphy'&& $type != 'taxonomy'&&$type != 'synonymies'&&$type != 'codes'&&$type != 'properties'&&$type != 'relationships') : ?>
      <tr>
        <th><?php echo $form['collection_ref']->renderLabel() ?> :</th>
        <td>
          <?php echo $form['collection_ref']->renderError() ?>
          <?php echo $form['collection_ref'] ?>
        </td>
      </tr>
      <?php endif ?>
      <!--ftheeten 2018 09 24-->
      <?php if($type == 'abcd') : ?>
          <tr>
            <th><?php echo $form['specimen_taxonomy_ref']->renderLabel() ?> :</th>
            <td>
              <?php echo $form['specimen_taxonomy_ref']->renderError() ?>
              <?php echo $form['specimen_taxonomy_ref'] ?>
            </td>
          </tr>
          <tr>
            <th><?php echo $form['enforce_code_unicity']->renderLabel() ?> :</th>
            <td>
              <?php echo $form['enforce_code_unicity']->renderError() ?>
              <?php echo $form['enforce_code_unicity'] ?>
            </td>
          </tr>
      <?php endif ?>
	  <?php if($type == 'properties'||$type == 'codes') : ?>
		<tr>
            <th><?php echo $form['update']->renderLabel() ?> :</th>
            <td>
              <?php echo $form['update']->renderError() ?>
              <?php echo $form['update'] ?>
            </td>
		</tr>
	  <?php endif; ?>
	  <?php if($type == 'synonymies') : ?>
		<tr>
            <th><?php echo $form['specimen_taxonomy_ref']->renderLabel() ?> :</th>
            <td>
              <?php echo $form['specimen_taxonomy_ref']->renderError() ?>
              <?php echo $form['specimen_taxonomy_ref'] ?>
            </td>
          </tr>
		  <tr>
            <th><?php echo $form['synonymy_taxonomy_ref']->renderLabel() ?> :</th>
            <td>
              <?php echo $form['synonymy_taxonomy_ref']->renderError() ?>
              <?php echo $form['synonymy_taxonomy_ref'] ?>
            </td>
          </tr>
	  <?php endif; ?>
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
        <th><?php echo $form['specimen_taxonomy_ref']->renderLabel() ?> :<br/><p style="font-weight:normal;">Please provide the phylum as higher rank (no domain or kingdom)</p></th>
        <td>
          <?php echo $form['specimen_taxonomy_ref']->renderError() ?>
          <?php echo $form['specimen_taxonomy_ref'] ?>
        </td>         
      </tr>
      <?php endif ?>
     <!--ftheeten 2018 09 24-->
      <?php if($type == 'taxon') : ?>
	  <tr>
      <th><?php echo $form['taxonomy_kingdom']->renderLabel() ?> :</th>
        <td>
          <?php echo $form['taxonomy_kingdom']->renderError() ?>
          <?php echo $form['taxonomy_kingdom'] ?>
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
