<?php foreach($collections as $id => $collection):?>
  <option value="<?php echo $id;?>"><?php echo $collections->getRaw($id); ?></option>
<?php endforeach;?>
