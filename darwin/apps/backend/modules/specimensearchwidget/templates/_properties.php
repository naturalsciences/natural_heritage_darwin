<table>
  <thead>
    <tr>
      <th><?php echo $form['property_type']->renderLabel(); ?>
        <?php echo $form['property_type']->renderError();?></th>
      <th><?php echo $form['property_applies_to']->renderLabel(); ?>
        <?php echo $form['property_applies_to']->renderError();?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php echo $form['property_type'];?></td>
      <td colspan="2"><?php echo $form['property_applies_to'];?></td>
    </tr>
    <tr>
    <th><?php echo $form['property_fuzzy']->renderLabel(); ?>
        <?php echo $form['property_fuzzy']->renderError();?>: <?php echo $form['property_fuzzy'];?></td>
    </tr>
    <tr>
      <th><?php echo $form['property_value_from']->renderLabel(); ?>
        <?php echo $form['property_value_from']->renderError();?></th>
      <th><?php echo $form['property_value_to']->renderLabel(); ?>
        <?php echo $form['property_value_to']->renderError();?></th>
      <th><?php echo $form['property_units']->renderLabel(); ?>
        <?php echo $form['property_units']->renderError();?></th>
    </tr>
    <tr>
      <td><?php echo $form['property_value_from'];?></td>
      <td><?php echo $form['property_value_to'];?></td>
      <td><?php echo $form['property_units'];?></td>
    </tr>
  </tbody>
</table>