<table>
  <tr>
    <th><?php echo $form['institution_ref']->renderLabel();?></th>
    <td><?php echo $form['institution_ref']->render() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo $form['building']->renderLabel();?></th>
	<td><?php echo $form['building']->render() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo $form['floor']->renderLabel();?></th>
	<td><?php echo $form['floor']->render() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo $form['room']->renderLabel();?></th>
	<td><?php echo $form['room']->render() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo $form['row']->renderLabel();?></th>
	<td><?php echo $form['row']->render() ?></td>
  </tr>
    <tr>
  <th class="top_aligned"><?php echo $form['col']->renderLabel('Column');?></th>
  <td><?php echo $form['col']->render() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo $form['shelf']->renderLabel();?></th>
	<td><?php echo $form['shelf']->render() ?></td>
  </tr>
</table>
