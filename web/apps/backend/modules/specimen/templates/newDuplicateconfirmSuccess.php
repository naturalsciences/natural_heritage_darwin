<div class="page">
<div class="edition">
  <p class="check_right">
<div><b>New duplicate</b></div>
<?php
echo form_tag('specimen/edit?id='.strval($duplicate_id)."&duplicate_mode=yes", array('class'=>'edition no_border','enctype'=>'multipart/form-data'));
?>
<table>
<tr>
<td>Do you want to create a relationship between the duplicate and the original specimen?</td><td>Yes:<input type="radio" name="create_duplicate_relationship" value="yes" checked="checked"></td><td>No:<input type="radio" name="create_duplicate_relationship" value="no"></td>
</tr>
</table>
<input type="hidden" name="origin_id" value="<?php print($origin_id);?>">
 <input type="submit" value="<?php echo __('Save');?>" id="submit_spec_f1"/>
</form>
</p>
</div>
</div>