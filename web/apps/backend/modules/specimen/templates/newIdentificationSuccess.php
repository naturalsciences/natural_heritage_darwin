<div class="page">
<div class="edition">
  <p class="check_right">
<div><b>new identification</b></div>
<?php
echo form_tag('specimen/edit?id='.strval($split_id)."&split_mode=yes", array('class'=>'edition no_border','enctype'=>'multipart/form-data'));
?>
<table>
<tr>
<td>Do you want to create a "date label" property?</td><td>Yes:<input type="radio" name="create_date_label" value="yes" checked="checked"></td><td>No:<input type="radio" name="create_date_label" value="no"></td>
</tr>
<tr>
<td>Do you want to consider this identifications as valid?</td><td>Yes:<input type="radio" name="valid_label" value="yes" checked="checked"></td><td>No:<input type="radio" name="valid_label" value="no"></td>
</tr>
<tr>
<td>Do you want to invalidate the other identifications?</td><td>Yes:<input type="radio" name="invalid_labels" value="yes" checked="checked"></td><td>No:<input type="radio" name="invalid_labels" value="no"></td>
</tr>
<tr>
<td>User</td>
<td colspan="2">
	<input type="text" name="label_author" value="<?php print($label_author);?>">
</td>

</tr>
</table>
<input type="hidden" name="origin_id" value="<?php print($origin_id);?>">
 <input type="submit" value="<?php echo __('Save');?>" id="submit_spec_f1"/>
</form>
</p>
</div>
</div>