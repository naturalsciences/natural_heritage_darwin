<?php foreach($fields as $field => $name) : ?>
  <?php if(isset($fields_options[$field]['second_line'])) : ?>
    <tr class="<?php echo $form[$field]->renderId().((isset($model_name))?'_'.$model_name:'');?>"><th colspan="<?php echo count($fields)+2-$fields_at_second_line ; ?>"><?php echo $form[$field]->renderLabel() ; ?></th></tr>
    <tr class="<?php echo $form[$field]->renderId().((isset($model_name))?'_'.$model_name:'');?>">
      <td colspan="<?php echo count($fields)+2-$fields_at_second_line ; ?>"><?php echo $form[$field]->render() ; ?></td>
    </tr>
  <?php endif; ?>
<?php endforeach; ?>
